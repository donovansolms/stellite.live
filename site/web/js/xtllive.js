/*
  Primary JS for stellite.live
 */
$(document).ready(function(){
  window.setInterval(function(){
    $.get('/site/index', function(data) {
      $('#circulation').html(data.circulation);
      $('#market_cap').html(data.market_cap);
      $('#price').html(data.price + ' BTC');
      $('#network_hashrate').html(data.hashrate);
      $('#network_difficulty').html(data.difficulty);
      $('#network_height').html(data.height);
      $('#trading_volume').html(data.volume + ' BTC');
      $('#trading_tradeogre_volume').html(data.volume_tradeogre + ' BTC');
      $('#trading_crex_volume').html(data.volume_crex + ' BTC');
      $('#record_volume').html(data.records.volume + ' BTC');
      $('#record_price').html(data.records.price + ' BTC');
    });
  }, 30 * 1000);
});
