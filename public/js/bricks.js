const settings = {
  /** カード右と下のマージン */
  margin: {width: 20, height: 10},
  /** 親要素のID */
  wrapId: 'block-article',
  /** 高さ揃えをする要素 */
  cardClassName: 'card-article',
};

function setBlicks() {
  /** 全体のNode */
  let wrap = document.getElementById(settings.wrapId);
  /** カードのNode **/
  let cards = wrap.getElementsByClassName(settings.cardClassName);
  /** 全体の幅 */
  let wrapWidth = wrap.offsetWidth; //全体の幅
  /** カードの幅 */
  let cardWidth = cards[0].offsetWidth + settings.margin.width;
  /** カラムごとの高さ {array} */
  let colNum = Math.floor((wrapWidth + settings.margin.width) / cardWidth);
  if (colNum < 1) { colNum = 1; }
  else if (colNum > cards.length) { colNum = cards.length; }
  console.log(colNum);

  let colHeights = Array.apply(null, Array(colNum)).map(() => { return 0; }); //列数分の要素を0による初期化
  //console.log((wrapWidth + settings.margin.width) / cardWidth);

  let marginCentering = (wrapWidth + settings.margin.width - cardWidth * colNum) / 2;

  /** 最初の行かどうか {bool} */
  let firstRow = true;
  /** 一番低い高さ **/
  let lowestHeight = 0;
  /** 一番低い列 **/
  let lowestCol = 0;
  /** 一番高い列 (親要素の高さ) */
  let highestHeight = 0;
  Array.prototype.forEach.call(cards, function(card, cardId) {

    if (firstRow) {
      // 最初の行だけそのままの順序で並べる。以降は最も高さの小さい列に。
      lowestHeight = 0;
      lowestCol = cardId;
      if (cardId +1 >= colHeights.length) {
        firstRow = false;
      }
    } else {
      // 一番低い列を調べる
      lowestHeight = colHeights[0];
      lowestCol = 0;
      colHeights.forEach(function(_height, _col) {
        if (_height < lowestHeight) {
          lowestHeight = _height;
          lowestCol = _col;
        }
      });
    }

    // 高さは一番低い高さから 横は個数から
//    card.style.top = lowestHeight + settings.margin.height + 'px';
//    card.style.left = cardWidth * lowestCol + 'px';
    card.style.top = lowestHeight + settings.margin.height + 'px';
    card.style.left = marginCentering + cardWidth * lowestCol + 'px';
    colHeights[lowestCol] += card.offsetHeight + settings.margin.height;
  });

  // 親要素の高さを指定する
  colHeights.forEach(function(_height) {
    if (_height > highestHeight) {
      highestHeight = _height;
    }
  });
  wrap.style.height = highestHeight + 'px';
}

window.addEventListener('DOMContentLoaded', setBlicks());
window.addEventListener('load', setBlicks());

// リサイズ時に再計算
let timer = false;
window.addEventListener('resize', () => {
  if (timer !== false) { clearTimeout(timer); }
  timer = setTimeout(() => { setBlicks(); }, 200);
});
