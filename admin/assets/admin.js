function hidepop(){
  $("#popup").fadeOut("slow");
}

function showpop(){
  $("#popup").fadeIn("slow");
  setTimeout("hidepop()",12000);
}

$(function() {

  $(window).bind('hashchange', function() {

    var hash = window.location.hash.substring(1);

    $("nav li").removeClass("active");
    $("html").scrollTop();
    $("window").scrollTop();

    if(window.location.hash != ""){
      $("main").load(hash);
    }else{
      window.location.hash = "#pages";
    }
    $("nav a[href='" + window.location.hash + "']").parent().addClass("active");
  });

$(window).trigger('hashchange');

$("nav li").removeClass("active");
$("nav a[href='" + window.location.hash + "']").parent().addClass("active");

});