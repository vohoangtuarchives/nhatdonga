<!DOCTYPE html>

<html lang="<?= $config['website']['lang-doc'] ?>">



<head>

    <?php include TEMPLATE . LAYOUT . "head.php"; ?>

    <?php include TEMPLATE . LAYOUT . "css.php"; ?>

</head>



<body>

    <?php

    include TEMPLATE . LAYOUT . "seo.php";

    include TEMPLATE . LAYOUT . "topbar.php";

    if ($source == 'index') include TEMPLATE . LAYOUT . "slide.php";

    else include TEMPLATE . LAYOUT . "breadcrumb.php";

    ?>

    <main class="wrap-main <?= ($source == 'index') ? 'wrap-home' : '' ?> w-clear" role="main">

        <?php include TEMPLATE . $template . "_tpl.php"; ?>

    </main>

    <?php

    include TEMPLATE . LAYOUT . "footer.php";

    if($config['cart']['active']==true){

        include TEMPLATE.LAYOUT."modal.php";

    }

    include TEMPLATE . LAYOUT . "phone.php";

    include TEMPLATE . LAYOUT . "js.php";

    include TEMPLATE . "components/dknt.php";

    ?>

</body>



</html>
