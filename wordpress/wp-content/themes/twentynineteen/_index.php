<?php
    include './wp-load.php';
    global $wpdb;
    $url="https://api.exchangeratesapi.io/latest?symbols=USD,GBP";
    $result= json_decode(file_get_contents($url), true);

    //add clicks to DB
    if(isset($_POST['target'])){
        $target = $_POST['target'];
        $q = " SELECT * FROM  `links` WHERE target = '{$target}'" ;
        $target_row = $wpdb->get_results($q);
        if(empty($target_row)){
            $wpdb->insert( 'links', array( 'target' => $target ));
        }else{
            $wpdb->update( 'links', array( 'clicks' => $target_row[0]->clicks+1), array( 'target' => $target ));
        }
        
    };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="public/scripts.js"></script>
    <link rel="stylesheet" type="text/css" href="public/style.css">
    <title>Investing.com</title>
</head>
<body>
    <nav>
        <div id="logo">Logo</div>
        <div id="links">
            <?php wp_nav_menu(array("menu" => "default")); ?>
        </div>
    </nav>
    <main class="row">
        <div class="left col-md-6">
        <?php 
        $loop = new WP_Query( array(
            'post_type'=> 'articles',
            'post_status' => 'publish',
            'order'    => 'ASC'
            ));

        if($loop->have_posts() ) : 
            $i = 0;
            while ( $loop->have_posts() ) : $loop->the_post(); 
            $new_row = $i % 2 == 0;
            
            $requested_currency = get_field('currency');
            $currency_rate = $result["rates"][$requested_currency] ?: 1;
            $price_format = number_format((float)get_field('price')*$currency_rate, 2, '.', '');
        ?>
            <?php if($new_row) echo '<div class="row">' ?>
                    <div class="col-sm-6">
                        <section>
                            <h2>
                                <span class="title"><?=the_title()?></span> 
                                <span class="price"><?=$price_format?></span>
                                <span class="rate">(from EUR to <?=$requested_currency?>, rate: <?=$currency_rate?>)</span>
                            </h2>
                            <p><?=the_content()?></p>
                        </section>
                    </div>
            <?php if(!$new_row) echo '</div>' ?>
        <?php $i++; endwhile; endif; ?>
        </div>
        <div class="right col-md-6">
        <?php 
        $loop = new WP_Query( array(
            'post_type'=> 'introductions',
            'post_status' => 'publish',
            'order'    => 'ASC'
            ));

        if($loop->have_posts() ) : 
            while ( $loop->have_posts() ) : $loop->the_post(); 
        ?>
            <section>
                <h1><?=the_title()?></h1>
                <p class="col-sm-8">
                <?=get_the_content()?>
                </p>
            </section>
        <?php endwhile;endif;?>
        </div>
    </main>
    <?php
        $result = $wpdb->get_results('SELECT sum(clicks) as result_value FROM `links`');
    ?>
    <footer class="row">
        <div class="col-sm-12">
            <h4>All rights reserved.</h4>
            <p>count: <span id="count"><?=$result[0]->result_value?></span></p>
        </div>
    </footer>
</body>
</html>