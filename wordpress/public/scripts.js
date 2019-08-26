$(document).ready(function(){
    //links clicking functionality
    var count = $("#count");

    $('a[href]').on("click", function(e){
        var url = $(this).attr('href');

        if(url.indexOf(window.location.host) === -1 && url.charAt(0) !== '/'){
            e.preventDefault();
            window.open(url);
            count.text(Number.parseInt(count.text())+1);

            $.ajax({
                url: "/investing/wordpress/index.php",
                method: "POST",
                data: { target: url},
              });
        }
    });

    $('.left section').on("click", function(){
        var title = $(this).children('h2').children('.title').text();
        
        count.text(Number.parseInt(count.text())+1);

        $.ajax({
            url: "/investing/wordpress/index.php",
            method: "POST",
            data: { target: title},
          });
    });

    //set a function to get the params
    $.urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        return (results) ? results[1] : 0; 
    }

    //search query menu
    var menu = $.urlParam('menu');
    
    if(menu){
        menu = menu.split(',');
        menu.forEach(function(item){
            $('.menu-item').eq(Number.parseInt(item)).show();
        });
    }else{
        $('.menu-item').show();
    }
});