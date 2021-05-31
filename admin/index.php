<?php

/** @var Phroute\Phroute\RouteCollector $collector */

$collector->any('', function () {
    Reagordi::$app->context->setTitle(t('Reagordi - Admin Panel'));
    Reagordi::$app->context->setDescription(t('Reagordi - Admin Panel'));

    if (Reagordi::$app->context->request->cookie->get('verify_offline') != Reagordi::$app->config->get('education', 'priem', 'key')) {
        header('Location: /');
        exit();
    }

    ob_start();
    ?>
    <button type="button" class="btn btn-default waves-effect" onclick="cron();">Пересчёт рейтинга для абитуриентов
    </button>
    <script>
        function ep_loader(st) {
            if (st === false) {
                document.body.style.overflow = 'auto';
                document.getElementById('rde_load').style.display = 'none';
            } else {
                document.body.style.overflow = 'hidden';
                document.getElementById('rde_load').style.display = 'block';
            }
        }

        function cron() {
            ep_loader(true);
            $.ajax({
                url: '<?= HOME_URL ?>/priem/cron.html?key=<?= Reagordi::$app->config->get('education', 'priem', 'key') ?>',
                method: 'get',
                headers: {
                    "Reagordi-Ajax": 'XMLHttpRequest'
                },
                success: function (data) {
                    ep_loader(false);
                }
            });
        }
    </script>
    <?php
    Reagordi::$app->context->view->assign('conteiner', ob_get_clean());

    //throw new \Phroute\Phroute\Exception\HttpRouteNotFoundException();

    return Reagordi::$app->context->view->fech();
});
