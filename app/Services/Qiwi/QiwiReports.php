<?php

namespace App\Services\Qiwi;

use App\Helpers\MoneyHelper;
use App\Structures\Transaction;
use Symfony\Component\DomCrawler\Crawler;


trait QiwiReports {
    /**
     * Отчет за период.
     *
     * @param $start
     * @param $end
     * @return array
     */
    public function reportForDateRange($start, $end) {
        $query = [
                'daterange' => 'true',
                'start' => $start,
                'finish' => $end,
        ];
        return $this->fetchReport($query);
    }

    /**
     * @param array $query
     * @param int $start
     * @param int $size
     * @return array
     */
    protected function fetchReport(array $query, $start = 0, $size = 15) {
        $this->login();

        $response = $this->client->get('https://qiwi.com/report/list.action', [
                'query' => $query,
        ]);

        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);
        $items = $crawler
                ->filter('.reportsLine[data-container-name="item"]')
                ->each(function (Crawler $node, $i) use ($start, $size) {

                    // 'ignore' unnecessary nodes
                    if ($i < $start || $i >= $size + $start) return null;

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

                    return $transaction;
                });

        //remove nulls
        $items = array_filter($items, function ($item) {
            return $item !== null;
        });

        return $items;
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
        $this->login();

        $response = $this->client->get('https://qiwi.com/report/list.action', [
                'query' => $query,
        ]);

        $html = $response->getBody()->getContents();
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

    //    public function getIncome(array $query) {
    //        $this->login();
    //
    //        $response = $this->client->get('https://qiwi.com/report/list.action', [
    //                'query' => $query,
    //        ]);
    //
    //        $html = $response->getBody()->getContents();
    //        $crawler = new Crawler($html);
    //        $moneyText = $crawler->filter('.SuccessWithFail.expenditure .success')->first()->text();
    //        $items = MoneyHelper::moneyToFloat($moneyText);
    //
    //        return $items;
    //    }

    protected function setAmountSign($class) {
        $class = str_replace('IncomeWithExpend', '', $class);
        if (trim($class) == 'income') {
            return '+';
        } else {
            return '-';
        }
    }
}