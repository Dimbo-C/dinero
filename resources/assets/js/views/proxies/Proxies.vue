<template>
    <div>
        <page-header icon="fa-shield" title="Управление PROXY"></page-header>

        <div class="container-fluid">
            <div class="row m-b-lg">
                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="fa fa-signal fa-btn"></i>Статус PROXY адресов
                            </h3>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item">В очереди на проверку: <strong>0</strong> шт.</li>
                            <li class="list-group-item">В резерве для Администраторов: <strong>0</strong> шт.</li>
                            <li class="list-group-item">В резерве для системы: <strong>0</strong> шт.</li>
                            <li class="list-group-item">Арендовано: <strong>0</strong> шт. / <strong>0</strong> шт.</li>
                            <li class="list-group-item">Общее кол-во: <strong>0</strong> шт. / <strong>0</strong> шт.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="btn-group p-b-xs">
                        <router-link to="/proxies/admin" class="btn btn-success">
                            <i class="fa fa-btn fa-fw fa-sitemap"></i>PROXY для Администраторов
                        </router-link>
                    </div>
                    <div class="btn-group p-b-xs">
                        <router-link to="/proxies/system" class="btn btn-success">
                            <i class="fa fa-btn fa-fw fa-cubes"></i>PROXY для системных нужд
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>