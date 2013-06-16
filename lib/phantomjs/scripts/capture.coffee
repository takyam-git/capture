#5分間でタイムアウト
setTimeout(=>
  phantomjs.exit(1)
, 1000 * 60 * 5)

#load
system = require('system')
fs = require('fs')

#const
WIDTH = 1280
HEIGHT = 1024

#引数の数がおかしければ何もしない
phantom.exit(1) if system.args.length < 3 or system.args.length > 5

#引数からURLと保存先を取得
address = system.args[1]
output = system.args[2]

WIDTH = parseInt(system.args[3], 10) if system.args[3]? and !isNaN(system.args[3])
HEIGHT = parseInt(system.args[4], 10) if system.args[4]? and !isNaN(system.args[4])

#ページの読み込みとキャプチャの出力を実行
page = require('webpage').create()
page.viewportSize = { width: WIDTH, height: HEIGHT }
page.open address, (status) ->
  #エラーの場合は強制終了
  return phantom.exit 1 if status isnt 'success'

  #背景色のデフォルト値として白色を設定しておく（そうしないと透明になってしまうサイトが存在する）
  page.evaluate =>
    style = document.createElement('style')
    text = document.createTextNode('body { background: #fff }')
    style.setAttribute('type', 'text/css')
    style.appendChild(text)
    document.head.insertBefore(style, document.head.firstChild)

  #ページが開けたら少し待ってからレンダリングする
  window.setTimeout (->
    page.clipRect = {left: 0, top:0, width: WIDTH, height: HEIGHT}
    page.render(output)
    phantom.exit()
  ), 200