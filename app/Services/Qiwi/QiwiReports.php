<?php

namespace App\Services\Qiwi;

use App\Helpers\MoneyHelper;
use App\Structures\Transaction;
use Symfony\Component\DomCrawler\Crawler;


trait QiwiReports {
    /**
     * @param $start
     * @param $end
     * @param array $options
     * @return array
     */
    public function reportForDateRange($start, $end, $options = []) {
        $query = [
                'daterange' => 'true',
                'start' => $start,
                'finish' => $end,
        ];

        return $this->fetchReport($query, $options);
    }

    /**
     * @param array $query
     * @param $options
     * @return array
     */
    protected function fetchReport(array $query, $options) {
        //        $this->login();

        $size = isset($options['size']) ? $options['size'] : 20;
        $page = isset($options['page']) ? $options['page'] : 1;
        $start = ($page - 1) * $size;

        $html = $this->fetchHistoryPage($query);

        $crawler = new Crawler($html);
        $items = $crawler->filter('.reportsLine[data-container-name="item"]');
        $transactions = [];

        foreach ($items as $i => $node) {
            if ($i < $start) continue;
            if ($i >= $size + $start) break;

            $node = new Crawler($node);
            $transaction = new Transaction();
            $transaction->date = trim($node->filter('.date')->first()->text());
            $transaction->time = trim($node->filter('.time')->first()->text());
            $transaction->transaction = trim($node->filter('.transaction')->first()->text());
            $transaction->status = strtolower(str_replace('reportsLine status_', '', $node->attr('class')));
            $transaction->provider = trim($node->filter('.ProvWithComment .provider span')->first()->text());
            $transaction->opNumber = trim($node->filter('.ProvWithComment .provider .opNumber')->first()->text());
            $transaction->comment = trim($node->filter('.ProvWithComment .comment')->first()->text());
            $transaction->amount = trim($node->filter('.originalExpense span')->first()->text());
            $transaction->sign = $this->setAmountSign($node->filter('.IncomeWithExpend')->attr('class'));
            $transaction->commission = trim($node->filter('.commission')->first()->text());

            if ($transaction->status == "error") {
                $transaction->errorMessage = json_decode($node->filter('.IncomeWithExpend .operations .error')->attr('data-params'))->message;
            }

            $transactions[] = $transaction;
        };

        return $transactions;
    }

    /**
     * @param $start
     * @param $end
     * @return array
     */
    public function getTotals($start, $end) {

        $query = [
                'daterange' => 'true',
                'start' => $start,
                'finish' => $end,
        ];
        //        $this->login();

        $html = $this->fetchHistoryPage($query);
        $crawler = new Crawler($html);

        try {
            $moneyText = $crawler->filter('.SuccessWithFail.expenditure .success')->first()->text();
            $expenditure = MoneyHelper::moneyToFloat($moneyText);
        } catch (\Exception $exception) {
            $expenditure = 0;
        }

        try {
            $moneyText = $crawler->filter('.SuccessWithFail.income .success')->first()->text();
            $income = MoneyHelper::moneyToFloat($moneyText);
        } catch (\Exception $exception) {
            $income = 0;
        }

        return [
                'income' => $income,
                'expenditure' => $expenditure,
        ];
    }

    protected function fetchHistoryPage($query) {
        $key = "history-page-{$this->login}-{$query['start']}-{$query['finish']}";

        // set big ass timeout for loading big html pages such as history
        $this->setNewTimeout(120);
        return \Cache::remember($key, env("HISTORY_CACHE_STORAGE_TIME", 1), function () use ($query) {
            return $this->client->get(
                    'https://qiwi.com/report/list.action',
                    ['query' => $query,]
            )->getBody()->getContents();
        });
    }

    protected function setAmountSign($class) {
        $class = str_replace('IncomeWithExpend', '', $class);

        return (trim($class) == 'income') ? '+' : '-';
    }
}