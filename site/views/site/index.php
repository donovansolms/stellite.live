<div class="info">
  <div class="logo">
    <img src="/i/logo-large.png" />
  </div>
  <div class="section spec">
    <table class="stats">
      <tr>
        <td class="title">
          Ticker
        </td>
        <td>
          XTL
        </td>
      </tr>
      <tr>
        <td class="title">
          Supply
        </td>
        <td>
          21 billion
        </td>
      </tr>
      <tr>
        <td class="title">
          Circulation
        </td>
        <td>
          <!-- 2.1 billion (~10%) -->
          <span id="circulation"><?= $stats['circulation']; ?></span>
        </td>
      </tr>
      <tr>
        <td class="title">
          Market cap
        </td>
        <td>
          <!-- USD 3 million -->
          <span id="market_cap"><?= $stats['market_cap']; ?></span>
        </td>
      </tr>
      <tr>
        <td class="title">
          Price
        </td>
        <td>
          <!-- 0.00000020 BTC -->
          <span id="price"><?= $stats['price']; ?> BTC</span>
        </td>
      </tr>
    </table>
  </div>
  <div class="section network">
    <h3>Network</h3>
    <table class="stats">
      <tr>
        <td class="title">
          Hashrate
        </td>
        <td>
          <!-- 20 MH/s -->
          <span id="network_hashrate"><?= $stats['hashrate']; ?></span>
        </td>
      </tr>
      <tr>
        <td class="title">
          Difficulty
        </td>
        <td>
          <!-- 1 234 912 392 -->
          <span id="network_difficulty"><?= $stats['difficulty']; ?></span>
        </td>
      </tr>
      <tr>
        <td class="title">
          Height
        </td>
        <td>
          <!-- 108 000 -->
          <span id="network_height"><?= $stats['height']; ?></span>
        </td>
      </tr>
    </table>
  </div>
  <div class="section exchanges">
    <h3>Exchanges</h3>
    <table class="stats">
      <tr>
        <td class="title">
          Volume today
        </td>
        <td>
          <!-- 30.3264 BTC -->
          <span id="trading_volume"><?= $stats['volume']; ?> BTC</span>
        </td>
      </tr>
      <tr>
        <td class="title">
          TradeOgre
        </td>
        <td>
          <!-- 3.4222 BTC -->
          <span id="trading_tradeogre_volume"><?= $stats['volume_tradeogre']; ?> BTC</span>
        </td>
      </tr>
      <tr>
        <td class="title">
          Crex24
        </td>
        <td>
          <!-- 1.8239 BTC -->
          <span id="trading_crex_volume"><?= $stats['volume_crex']; ?> BTC</span>
        </td>
      </tr>
    </table>
  </div>
  <div class="section records">
    <h3>Records</h3>
    <table class="stats">
      <tr>
        <td class="title">
          Peak volume
        </td>
        <td>
          <!-- 30.3264 BTC -->
          <span id="record_volume"><?= $stats['records']['volume']; ?> BTC</span>
        </td>
      </tr>
      <tr>
        <td class="title">
          Highest price
        </td>
        <td>
          <!-- 0.000000023 BTC -->
          <span id="record_price"><?= $stats['records']['price']; ?> BTC</span>
        </td>
      </tr>
    </table>
  </div>
  <div class="section contact">
    <h3>Contact</h3>
    <div class="links">
      <a target="_blank" href="https://www.stellite.cash">Official Website</a>
      <a target="_blank" href="https://bitcointalk.org/index.php?topic=2813261.0">ANN</a>
      <a target="_blank" href="https://discord.gg/8PhF342">Discord</a>
      <a target="_blank" href="https://www.reddit.com/r/stellite/">Reddit</a>
      <a target="_blank" href="https://github.com/stellitecoin">GitHub</a>
      <a target="_blank" href="https://www.facebook.com/stellitecash">Facebook</a>
      <a target="_blank" href="https://twitter.com/stellitecash">Twitter</a>
      <a target="_blank" href="https://coinmarketcap.com/currencies/stellite">Coin Market Cap</a>
      <a target="_blank" href="https://coinlib.io/coin/XTL/Stellite">CoinLib</a>
      <a target="_blank" href="https://getdelta.io">Delta</a>
      <a target="_blank" href="https://www.blockfolio.com">BlockFolio</a>
    </div>
  </div>
</div>
<div class="pool-list">
  <h2>Pools (<?= count($pools)?>)</h2>
  <div class="divider"></div>
  <p class="description">
    Recommended pools
  </p>
  <?php foreach ($pools as $i => $pool): ?>
    <div class="pool" data-id="{{ .ID }}">
      <h3><?= $pool->name ?></h3>
      <a href="<?= $pool->url ?>" target="_blank" class="address"><?= $pool->url ?></a>
      <div class="stats">
        <table>
          <tr>
            <th>
              Hash Rate
            </th>
            <th>
              Miners
            </th>
            <th>
              Last Block Found
            </th>
          </tr>
          <tr>
            <td>
              <?= $pool->hashrate ?>
            </td>
            <td>
              <?= $pool->miners ?>
            </td>
            <td>
              <?= $pool->last_block ?>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <?php if ($i == 2): ?>
      <div class="divider"></div>
      <p class="description">
        Other pools
      </p>
    <?php endif; ?>
  <?php endforeach; ?>
</div>
