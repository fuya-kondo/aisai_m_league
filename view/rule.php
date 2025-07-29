<?php

// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';

// Set title
$title = 'AISAI.M.LEAGUE 競技ルール規定';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="apple-touch-icon" href="../favicon.png">
    <link rel="icon" href="../favicon.ico" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="../webroot/css/master.css">
    <link rel="stylesheet" href="../webroot/css/header.css">
    <link rel="stylesheet" href="../webroot/css/button.css">
    <link rel="stylesheet" href="../webroot/css/table.css">
    <title><?= $title ?></title>
</head>
<body>
<main>
    <div class="p-rule__body">
        <section class="p-rule__group">
        <h3 class="c-title">
            <span>第1章 競技の基本</span>
        </h3>
            <h3 class="c-title"></h3>
            <div class="p-rule__contents">
                <h4 class="c-title -dot">第1条 競技の構成</h4>
                <p class="p-rule__text">競技は1卓4人で行う、東南二風の半荘を以って1回戦とする。半荘に於いて、1周目を東場、2周目を南場とする。</p>
                <h4 class="c-title -dot">第2条 用具</h4>
                <ol class="p-rule__text">
                    <li>麻雀牌は、数牌108枚（萬子・筒子・索子の1～9各4枚）と字牌28枚（東・南・西・北・白・發・中の各4枚）の計136枚を一式とし、計二式を用いて競技を行う。</li>
                    <li>定められた一式の麻雀牌の内、5萬・5筒・5索の各1枚を赤牌とする。</li>
                    <li>麻雀卓は、自動配牌機能を有した全自動卓を使用する。</li>
                    <li>点棒は競技者1名につき、1万点棒1本・5千点棒2本・千点棒4本・5百点棒1本・百点棒5本・予備1万点棒2本を使用する。</li>
                    <li>起家の表示と各風位を示すもの（起家マーク）を使用する。</li>
                </ol>
                <h4 class="c-title -dot">第3条 縛り</h4>
                <p class="p-rule__text">アガリ時に一翻以上の役を必要とする。これを常時一翻縛りという。</p>
                <h4 class="c-title -dot">第4条 場ゾロ</h4>
                <p class="p-rule__text">アガリに対し、常時二翻を加えて計算する。</p>
                <h4 class="c-title -dot">第5条 配牌</h4>
                <ol class="p-rule__text">
                    <li>自動配牌機能を使用する。</li>
                    <li>自動配牌機能がない卓の場合は、取り出しを行う。</li>
                </ol>
                <h4 class="c-title -dot">第6条 発声</h4>
                <p class="p-rule__text">競技行為を対局者並びに審判に告知する為に次の発声をする。<br>1吃(チー) 2碰(ポン) 3槓(カン) 4立直(リーチ) 5栄和(ロン) 6自摸和(ツモ)</p>
                <h4 class="c-title -dot">第7条 対局者</h4>
                <ol class="p-rule__text">
                    <li>対局者は本規定を遵守し、公平なプレーを行う義務がある。</li>
                    <li>競技中に疑義が生じた場合は審判が裁定し、対局者はそれに従わないとならない。</li>
                    <li>対局者は、対局中の疑義について審判に訴えることはできるが、裁定後の再提訴は認めない。</li>
                </ol>
                <h4 class="c-title -dot">第8条 審判</h4>
                <ol class="p-rule__text">
                <li>麻雀対局における知識や経験など、専門性を有する人材を、一般社団法人Mリーグ機構が審判として認定した者が務める。</li>
                <li>用具がその規定に合致している事を、競技開始前に確認する。</li>
                <li>競技規則を施行し、対局をコントロールする責務を担う。</li>
                <li>競技規則の範囲におけるすべての権限を有し、競技上での疑義・裁定において最終決定者である。<br>また、競技規定の範囲の定めから外れる不測の事態が起こった際も同様の権限を持つ。</li>
                <li>審判は試合の中断、再開、終了、の権限を持つ。</li>
                </ol>

            </div>
        </section>

        <section class="p-rule__group">
            <h3 class="c-title">
                <span>第2章 手牌・王牌・海底牌・河底牌・嶺上牌・懸賞牌</span>
            </h3>
            <div class="p-rule__contents">
                <h4 class="c-title -dot">第1条 手牌</h4>
                <p class="p-rule__text">手牌は 13 枚を原則とし、一槓ある毎に1枚増す。</p>
                <h4 class="c-title -dot">第2条 王牌</h4>
                <p class="p-rule__text">開門後の壁牌末尾より数えて 14 枚を残し、これを王牌という。</p>
                <h4 class="c-title -dot">第3条 海底牌</h4>
                <p class="p-rule__text">王牌直前の牌を海底牌という。海底牌を摸した者は槓をすることができない。</p>
                <h4 class="c-title -dot">第4条 河底牌</h4>
                <p class="p-rule__text">海底牌を摸した者と、海底直前の牌を摸し槓をした者は、自己にアガリがなければ、任意の牌を1枚捨てなければならない。この捨てられた牌を河底牌という。河底牌を「チー・ポン・カン」する事はで きない。</p>
                <h4 class="c-title -dot">第5条 嶺上牌</h4>
                <p class="p-rule__text">槓子ができた時は、王牌最後尾から1枚を摸して補充する。この補充牌を嶺上牌という。嶺上牌は王牌の最尾幢上段から順次に取る。嶺上牌は槓子を明示してからでなければ取ることができない。</p>
                <h4 class="c-title -dot">第6条 懸賞牌(ドラ)</h4>
                <ol class="p-rule__text">
                    <li>本ドラ<br>牌山が上がった際に、表に向けられている牌をドラ表示牌といい、その次順牌(三元牌は便宜上、白・發・中の順)をドラとする。</li>
                    <li>槓ドラ<br>ドラは槓が有る事に増え、一つ目の槓では王牌末尾より4幢目の上段の牌を表に向け、これが新ドラ表示牌となる。以後この行為を繰り返し、四つ目の槓では7幢目となる。</li>
                    <li>裏ドラ<br>立直者のアガリには裏ドラがつく。すべての表ドラ表示牌の下段牌が裏ドラ表示牌となる。槓ドラにも裏ドラはつく。</li>
                    <li>赤ドラ5萬・5筒・5索の各1枚をドラとする。</li>
                    <li>1−4全てのドラは1枚につき1翻とし、役とはならない。</li>
                    <li>牌山が上がった際にドラ表示牌が表に向けられてなかった場合は、開門（第3章4条2参照）された王牌末尾より右3幢目上段の牌を表に向ける。</li>
                </ol>
            </div>
        </section>

        <section class="p-rule__group">
            <h3 class="c-title">
                <span>第3章 競技の進行</span>
            </h3>
            <div class="p-rule__contents">
                <h4 class="c-title -dot">第1条 座位と起家の決定</h4>
                <ol class="p-rule__text">
                    <li>対局者4人が任意の場所に座り、任意の一名が「東・南・西・北」の4牌を伏せた状態でかき混ぜた後、横一列に並べる。</li>
                    <li>前対局で着順の高い選手から順に1人ずつ、任意の牌を1枚選ぶ。選ばれた牌が「東」であった場合、前試合での東家の席を仮東とし着座する。初対局の場合は任意の選手から牌を選ぶ</li>
                    <li>仮東の者がサイコロを振り、出目によって指定された家の者が仮親となる。仮親の者が再度サイコロを振り、出目によって指定された家の者が起家となる。</li>
                </ol>
                <h4 class="c-title -dot">第2条 競技の開始および終了</h4>
                <ol class="p-rule__text">
                    <li>対局日の初戦開始時は、審判によって運ばれた麻雀牌二式の内、一式を自動卓内に入れ、一式を卓上に置く。それ以降は、卓内にある一式を取り出し、崩した後に開始する。</li>
                    <li>競技は起家がスタートボタンを押した時点で開始とし、一局の開始も同様とする。</li>
                    <li>アガリ、または流局、あるいはチョンボによる終局を以って一局とする。途中流局はない。</li>
                    <li>半荘途中で持ち点が無くなった場合でも最終局が終了するまで続行する。</li>
                    <li>予備の1万点棒2本が無くなった際は、卓外から3万点単位で点棒の補充をする</li>
                </ol>
                <h4 class="c-title -dot">第3条 洗牌および築牌</h4>
                <ol class="p-rule__text">
                    <li>洗牌（牌をよくかき混ぜる行為）は、開始時・各局終了時共通で行う必要がない。</li>
                    <li>対局者は、各局終了時に速やかに卓内に牌を流すこと。</li>
                    <li>対局者は開始時の自山を井桁形に組む。これを築牌という。</li>
                    <li>築牌時に山を崩した場合、状態によって罰則の対象となることがある。</li>
                </ol>
                <h4 class="c-title -dot">第4条 開門と配牌</h4>
                <ol class="p-rule__text">
                    <li>自動配牌機能を使用する。</li>
                    <li>開門とは、王牌の末尾と親の第一自摸（ツモ）の場所を定める行為である。これも自動機能を用いる。</li>
                    <li>対局者は、ドラ表示牌の表示と開門が正しく行われた事を確認するまで、配牌を開けてはならない。</li>
                    <li>親は前項2・3が終わったことを確認した後に第一ツモを行う。</li>
                    <li>開門がされておらず、ドラ表示牌は表示されている状態で山が上がった際は、ドラ表示牌から左に3幢目で開門する。</li>
                    <li>開門がされておらず、ドラ表示牌も表示されていない状態で山が上がった際は、親が自7（自山の右から7幢目）で開門し、王牌末尾より右3幢目上段の牌を表に向ける。</li>
                    <li>親が第一ツモをせずに打牌した場合、その打牌は成立し、親の少牌のままアガリ放棄として進行する。</li>
                    <li>親の第一ツモを他家がツモった場合は、ツモ牌を所定の位置に戻し、当該者をアガリ放棄として進行する。</li>
                    <li>その他局の開始時に不測の事態が起きた場合は、状況に応じて審判が裁定を行う。</li>
                </ol>
                <h4 class="c-title -dot">第5条 親の順序</h4>
                <p class="p-rule__text">親の順序は起家より左廻りに移動する。親の順番間違いは、発覚次第、正当な親に直す。正当でない局は無効とし、その間に点棒の授受があれば、元に戻す。</p>
                <h4 class="c-title -dot">第6条 取牌行為</h4>
                <ol class="p-rule__text">
                    <li>取牌行為は次の5種とする。〔ツモ〕 〔チー〕 〔ポン〕 〔カン〕 〔アガリ〕</li>
                </ol>
                <h4 class="c-title -dot">第7条 摸打</h4>
                <ol class="p-rule__text">
                    <li>摸打とはツモから打牌までの行為をさす。順序は、摸が先で打は後でなければならない。</li>
                    <li>「摸打の一巡」とは、自己の打牌から次回の自己の取牌直前までとする。</li>
                    <li>掌中に他の牌を握ったまま摸打をしてはならない。</li>
                    <li>摸打の行為は片手のみで行うこと。原則として利き手を使用する。</li>
                    <li>競技とその進行に影響を及ばさない様に、摸打のペースが早すぎる、遅すぎることは避ける。</li>
                </ol>
                <h4 class="c-title -dot">第8条 自摸(ツモ)</h4>
                <ol class="p-rule__text">
                    <li>ツモとは壁牌から1枚とる行為をいう。</li>
                    <li>ツモの目的でツモ動作行為に入る事をツモ行為の開始とする。壁牌に触れた時点で、「チー・ポン・カン・ロン」の行為ができない。</li>
                    <li>ツモの行為は、上家の捨て牌行為の完了を確認した後に行う。</li>
                    <li>先ヅモは、審判がこれとみなし宣告した時点でアガリ放棄となる。</li>
                    <li>ツモった牌を手牌に入れてはならない。</li>
                </ol>
                <h4 class="c-title -dot">第9条 打</h4>
                <ol class="p-rule__text">
                    <li>打とは、ツモした後に任意の牌を1枚河に捨てる行為および、「チー・ポン・カン」の行為完了後に任意の牌を1枚捨てる行為をいう。</li>
                    <li>打牌が河についた瞬間から捨て牌となり、その後取り戻すことはできない。</li>
                    <li>河は自山前のスペースを基準とする。それ以外のスペースに牌を置いた際にも打牌と判断される場合がある。</li>
                    <li>強打は禁止としないが、度重なる場合は罰則対象になることがある。</li>
                </ol>
                <h4 class="c-title -dot">第10条 捨て牌</h4>
                <ol class="p-rule__text">
                    <li>捨て牌は左から右へ順に並べて捨てる。</li>
                    <li>捨て牌は一列を6枚とし、二列目はその下段に、三列目は更にその下に並べる。</li>
                    <li>みだりに捨て牌に触れてはならない。</li>
                </ol>
                <h4 class="c-title -dot">第11条 流局と聴牌</h4>
                <ol class="p-rule__text">
                    <li>流局とは、アガリ者の出なかった局の事をいう。いかなる場合でも、局の途中で流局はしない。</li>
                    <li>聴牌とはアガリが可能となった状態だが、自己の手牌・副露牌でアガリ牌が消去されている場合は認められない。</li>
                    <li>流局時の聴牌は、手牌の開示を以って宣言とする(裸単騎のノーテン宣言も可能) 。手役の有無は問わない。</li>
                    <li>聴牌の宣言は、原則として東・南・西・北家の順で行う。</li>
                    <li>聴牌料は場に3,000点とする。</li>
                </ol>
                <h4 class="c-title -dot">第12条 連荘（レンチャン）</h4>
                <ol class="p-rule__text">
                    <li>親が続けて局を行うことを連荘という。</li>
                    <li>連荘は親のアガリと聴牌宣言を行った時とする。親がノーテンの場合は、下家に親が移動する。<br>(注 ・チョンボがあった場合、その局はやり直しとし、チョンボをした者が親・子にかかわらず、再競技となる。)</li>
                </ol>

                <h4 class="c-title -dot">第13条 積み場</h4>
                <ol class="p-rule__text">
                    <li>連荘および親がノーテンで流局した際は、次の局を積み場とし、以後回数と共に増やしていく。</li>
                    <li>積み場の表示は、卓中央部のデジタル表示のみとし、百点棒は卓に出さない。</li>
                    <li>一本場につき300点をアガリ点に加算する。</li>
                    <li>子のアガリを以って積み棒は消滅する。</li>
                </ol>
            </div>
        </section>

        <section class="p-rule__group">
            <h3 class="c-title">
                <span>第4章 競技の詳細</span>
            </h3>
            <div class="p-rule__contents">
                <h4 class="c-title -dot">第1条 競技行為</h4>
                <ol class="p-rule__text">
                    <li>発声を必要とする競技行為は、発声を以って開始とし、行為完了を以って終了とする。</li>
                    <li>競技行為の確認は審判が行うが、対局者も他家の競技行為を確認し、競技を進行させる責任を相互に持たねばならない。</li>
                </ol>
                <h4 class="c-title -dot">第2条 優先順位</h4>
                <ol class="p-rule__text">
                    <li>競技行為の優先順位は次のとおりとする。 1アガリ 2ポン・カン 3チー</li>
                    <li>ポン・カン・チーの発声が被った場合は、順位通りに優先されるが、発声が遅れた場合や聞こえなかった場合は審判の裁定により判断される。認められなかった行為は罰則対象にならない。</li>
                    <li>チーの行為を開始した後のポン・カン・ロンは、審判に著しく遅いと判断された場合は反則行為として、所定の罰を受けることがある。</li>
                </ol>
                <h4 class="c-title -dot">第3条 吃(チー)</h4>
                <ol class="p-rule__text">
                    <li>チーとは、発声 のあと、手中の搭子を開示して上家の捨て牌をその搭子に加え順子を作り、自己の右側へ副露法(第 6条参照、以下ポン・カンも同様)に従って副露し、任意の1枚を捨てる行為をいう。</li>
                    <li>同時発声の場合は審判の裁定に従う。 この場合で成立しなかったチーは空チーとはならない。<br>（以下ポン・カンも同様）</li>
                </ol>
                <h4 class="c-title -dot">第4条 碰(ポン)</h4>
                <ol class="p-rule__text">
                    <li>ポンとは、他家の捨てた牌に対し、直ちに「ポン」と発声し、手中の対子を開示し、その牌を加えて刻子を作り、副露法に従って副露し、任意の1枚を捨てる行為をいう。</li>
                    <li>同時発声の場合は審判の裁定に従う。</li>
                </ol>
                <h4 class="c-title -dot">第5条 槓(カン)</h4>
                <ol class="p-rule__text">
                    <li>槓は暗槓と明槓の2種類がある。</li>
                    <li>暗槓とは、牌を摸した後「カン」と発声し、手中(ツモ牌を含む)にある4枚の同一 牌(槓子)を開示し、その内の内側または外側2枚を伏せて自己の地の右側へ出し、補充牌として王牌末尾牌を1枚取る行為をいう。リーチ後の暗槓は面子構成の変わらない暗槓なら可能。(役の増減は問わない)</li>
                    <li>明槓の取り決めは以下の通りとする。 <br>
                    （1）加槓　 牌を摸した後「カン」と発声し、自己の明刻子に手中(ツモ牌を含む)より同一牌を加え、補充牌として王牌末尾の牌を1枚取る行為をいう。<br>
                    （2）大明槓 　他家の捨て牌に対し直ちに「カン」と発声し、手中の暗刻子を開示しその牌を加えて槓子を作り、自己の地の右側に副露法に従って副露し、補充牌として王牌末尾の牌を1枚取る行為をいう。<br>
                    （3）同時発声の場合は審判の裁定に従う。また、審判に著しく遅いと認められた場合は罰則対象となることがある。</li>
                    <li>一局中の開槓数は全体で4つまでとする</li>
                    <li>槓ドラは槓子の開示が確認された時点で発生、速やかに槓ドラ表示牌を表示する。搶槓(チャンカン)により槓が成立しない時、槓ドラは表示されない。</li>
                    <li>いかなる場合でも、暗槓の搶槓は成立しない。</li>
                </ol>
                <h4 class="c-title -dot">第6条 副露牌および副露法</h4>
                <p class="p-rule__text">チー・ポン・カンによって卓の右側に公開された牌を副露牌という。 （暗槓は副露の対象とはならない）副露法は以下のとおりとする。<br>
                （1） 明順子　チーした牌を横向きし、手牌から開示した搭子の左に並べる。<br>
                （2） 明刻子　ポンした牌を横向きにし、手牌から開示した対子に加える。上家からは左、対面からは真ん中、下家からは右に並べる。<br>
                （3） 明槓子(大明槓によるもの)　カンした牌を横向きにし、手牌から開示した暗刻に加える。上家からは左、対面から左2番目、下家からは右に並べる。<br>
                （4） 加槓子　加槓牌を指示牌の上に並べて重ねる。<br>
                （5） （1）〜（4）の副露牌は自己の地の右側隅に置く。複数の際は確定した順に自己から見て手前から奥へと順に、縦に並べる。暗槓子も同様とする。</p>
                <h4 class="c-title -dot">第7条 指示牌</h4>
                <p class="p-rule__text">チー・ポン・カンの指示牌および指示方向を間違えて進行し、該当局内に対局者が気付いた場合、全員の同意があれば、審判の指示がなくとも訂正可能とする。間違えた状態で起こった事象の裁定は審判が行う。</p>
                <h4 class="c-title -dot">第8条 立直(リーチ)</h4>
                <ol class="p-rule__text">
                    <li>リーチは、門前清（メンゼン）であればかける事ができ、リーチ宣言牌に対してロンがなければ成立する。</li>
                    <li>リーチ宣言は、「リーチ」と発声し、打牌を横に向けて置く。そして供託棒(リーチ棒)として千点棒1本を所定の場所に置く。「リーチ」の発声があっても、打牌の前なら取り消しすることが可能である。ただし、その場合は空行為となりアガリ放棄となる。打牌後の取り消しはできない。</li>
                    <li>フリテンリーチやツモ番のないリーチもかける事ができる。(海底牌を摸した者はリーチを掛ける事はできない。)</li>
                    <li>リーチ宣言牌に対して、チー・ポン・カンがあった場合は、次巡の打牌を横にする。</li>
                    <li>リーチ棒は以降のアガリ者が取得する。（但し、南四局が流局となった場合はトップ者に加算される）</li>
                    <li>リーチ後でもアガリの見送りができるが、以後はフリテン扱いとなる。</li>
                    <li>リーチ後の暗槓も可能（本章第5条-2 参照）</li>
                </ol>
            </div>
        </section>

        <section class="p-rule__group">
            <h3 class="c-title">
                <span>第5章 和了（アガリ）</span>
            </h3>
            <div class="p-rule__contents">
                <h4 class="c-title -dot">第1条 アガリ</h4>
                <ol class="p-rule__text">
                    <li>アガリの形式は、雀頭＋４面子・七対子・十三幺九（国士無双）とする。</li>
                    <li>アガリの手段は、次の2種類とする。<br>
                    （1）自摸和… 自己のツモにてあがる事。<br>
                    （注・嶺上牌によるアガリはすべてツモアガリとする）<br>
                    （2）栄和 ... 他家の打牌にてあがる事。アタリ牌を捨てた者、搶槓された者を放銃者とする。
                    </li>
                    <li>アガリ者は一局に1人とする。(ひとつの牌に2人以上のアガリ宣言があった場合は、放銃者の下家、対面、上家の順に権利を発する)これを「頭ハネ」ともいう。</li>
                    <li>摸打の一巡内でのアガリ牌の選択はできない。</li>
                </ol>
                <h4 class="c-title -dot">第2条 アガリ宣言</h4>
                <ol class="p-rule__text">
                    <li>アガリ者は、ツモアガリの場合は「ツモ」、ロンアガリの場合は「ロン」と発声し、手牌を開示する。</li>
                    <li>ツモアガリの場合は、まずアガリ牌を明示すること。ツモ牌(アガリ牌)を手牌に加えてからのアガリ宣言もアガリとして認められる。その場合は、審判がアガリ牌の明示を指示し、その後点数申告を行う。</li>
                    <li>審判に著しく遅いと認められたロンアガリは罰則対象となることがある。</li>
                </ol>
                <h4 class="c-title -dot">第3条 アガリの確認</h4>
                <p class="p-rule__text">他家からのアガリの宣言があった場合は、対局者全員がアガリ形を確認するまでは、手牌、捨て牌および壁牌を崩さないこと。</p>
                <h4 class="c-title -dot">第4条 アガリ役の複合</h4>
                <p class="p-rule__text">共に同居しうる役の複合を認められる。また、異なる役満が複合した場合も認められる。<br>【例】嶺上開花と海底摸月は複合しない。</p>
                <h4 class="c-title -dot">第5条 振聴牌(フリテン)</h4>
                <ol class="p-rule__text">
                    <li>自己の捨て牌にアガリ形を構成できる牌がある聴牌をフリテンという。</li>
                    <li>フリテンの場合のアガリはツモアガリに限る。</li>
                    <li>「摸打の一巡」（第３章第７条参照）の間に、自己のアガリ牌が出、その牌でロンをしなかった場合、フリテン（同巡内フリテン）となる。同巡内フリテンは自己の摸打の完了を経て解消される。</li>
                    <li>リーチ後にアガリ牌が出た場合に、リーチ者はロンをしない（見逃す）ことができる。その後はフリテンとなりツモアガリのみ有効となる。</li>
                </ol>
            </div>
        </section>

        <section class="p-rule__group">
            <h3 class="c-title">
                <span>第6章 計算(収支および得点)</span>
            </h3>
            <div class="p-rule__contents">
                <h4 class="c-title -dot">第1条 原点</h4>
                <p class="p-rule__text">各自の持ち点を25,000点とし、これを原点とする。(対局者は競技開始に先立って自己の持ち点を確かめておく義務がある)</p>
                <h4 class="c-title -dot">第2条 持ち点・順位点</h4>
                <ol class="p-rule__text">
                    <li>持ち点は、30,000点を超える得点をプラス（＋）、不足する失点をマイナス（▲）とする。</li>
                    <li>競技順位は、半荘終了時の合計得点の多少によって決定する。</li>
                    <li>順位点とは、半荘での競技順位に従って加減算される点であり、本規定では、以下に示す方式を用いる。<br>
                    1位＋50,000点　2位+10,000点　3位▲10,000点　4位▲30,000点</li>
                    <li>持ち点・順位点共に、1000点＝1ポイントに換算し成績を集計する。<br>【例】35,800点の2位→+5.8p+10.0p(順位点)=+15.8p<br> 　41,600点の1位→+11.6p+50.0p（順位点）=+61.6p</li>
                    <li>流局で半荘が終了した際のリーチ棒は、トップ者に加算される。</li>
                    <li>半荘終了時に同点で終わった場合、順位点を分ける。3名が同点だった場合の端数は起家に近い方が大きいポイントを取得する。流局終了した際のリーチ棒は当該者で均等に分ける。3名の場合は、1000点を400・300・300に分け、割り切れない場合はこれを等倍する。（2000点の場合、800・600・600）。<br>順位点と同様に、起家に近い方が大きいポイントを取得する。</li>
                </ol>
                <h4 class="c-title -dot">第3条 得点計算(1)</h4>
                <ol class="p-rule__text">
                    <li>
                        副底、門前清栄和加符および部分符とツモ符<br>
                        ・ 副底　20符<br>
                        ・ 門前清栄和加符　10符 <br>
                        ・辺張・嵌張および単騎　2符<br>
                        ・自摸符　2符 (注・ツモ符は、平和をツモアガリした時以外は全て認められる)
                    </li>
                    <li>組み合わせに基づく部分符は以下のとおりとする

                    <table class="p-rule__table">
                        <tbody><tr>
                            <th></th>
                            <th></th>
                            <th>中張牌</th>
                            <th>老頭牌 客風牌</th>
                            <th>翻牌</th>
                        </tr>
                        <tr>
                            <td>対子</td>
                            <td></td>
                            <td>0</td>
                            <td>0</td>
                            <td>2符</td>
                        </tr>
                        <tr>
                            <td>刻子</td>
                            <td>明刻</td>
                            <td>2符</td>
                            <td>4符</td>
                            <td>4符</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>暗刻</td>
                            <td>4符</td>
                            <td>8符</td>
                            <td>8符</td>
                        </tr>
                        <tr>
                            <td>槓子</td>
                            <td>明刻</td>
                            <td>8符</td>
                            <td>16符</td>
                            <td>16符</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>暗刻</td>
                            <td>16符</td>
                            <td>32符</td>
                            <td>32符</td>
                        </tr>
                    </tbody></table>

                    ＊連風牌の対子も2符とする</li>
                    <li>連底の計算 副底、門前清栄和加符、部分符、自摸符を合計したものを連底といい、計算は次のとおりとする。<br>
                        ・ 門前清栄和 副底+門前清栄和加符+部分符<br>
                        ・門前清自摸和 副底+自摸符+部分符<br>
                        ・栄和(副露面子のあるもの) 副底+部分符<br>
                        ・自摸和(副露面子のあるもの) 副底+自摸符+部分符
                    </li>
                    <li>連底は1の位の端数を10の位に切り上げる。</li>
                    <li>点数計算の慣例により、副露した平和形栄和には、副底に10符を加える。</li>
                </ol>
                <h4 class="c-title -dot">第4条 得点計算(2)翻の計算とアガリ点</h4>
                <ol class="p-rule__text">
                    <li>第3条に従って求めた数字に〈2の翻数乗〉を掛け算してアガリ点を求める。翻数には場ゾロの2翻を加える。</li>
                    <li>アガリ点は以下とする。（翻数は場ゾロの2翻を含んだもの）また、30符6翻は切り上げ満貫とする。
                        <table class="p-rule__table -full">
                            <tbody><tr>
                                <th scope="col">親</th>
                                <th scope="col">3翻</th>
                                <th scope="col">4翻</th>
                                <th scope="col">5翻</th>
                                <th scope="col">6翻</th>
                            </tr>
                            <tr>
                                <th scope="row">20符（ツモピンフ)</th>
                                <td></td>
                                <td>700オール</td>
                                <td>1300オール</td>
                                <td>2600オール</td>
                            </tr>
                            <tr>
                                <th scope="row">25符（チートイツ)</th>
                                <td></td>
                                <td>2400</td>
                                <td>4800 (1600)</td>
                                <td>9600 (3200)</td>
                            </tr>
                            <tr>
                                <th scope="row">30符</th>
                                <td>1500<br>(500オール/ほか同）</td>
                                <td>2900 (1000)</td>
                                <td>5800 (2000)</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">40符</th>
                                <td>2000 (700)</td>
                                <td>3900 (1300)</td>
                                <td>7700 (2600)</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">50符</th>
                                <td>2400 (800)</td>
                                <td>4800 (1600)</td>
                                <td>9600 (3200)</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">60符</th>
                                <td>2900 (1000)</td>
                                <td>5800 (2000)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">70符</th>
                                <td>3400 (1200)</td>
                                <td>6800 (2300)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">80符</th>
                                <td>3900 (1300)</td>
                                <td>7700 (2600)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">90符</th>
                                <td>4400 (1500)</td>
                                <td>8700 (2900)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">100符</th>
                                <td>4800 (1600)</td>
                                <td>9600 (3200)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">110符</th>
                                <td></td>
                                <td>10600 (3600)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                        </tbody></table>
                        <table class="p-rule__table -full">
                            <tbody><tr>
                                <th scope="col">子</th>
                                <th scope="col">3翻</th>
                                <th scope="col">4翻</th>
                                <th scope="col">5翻</th>
                                <th scope="col">6翻</th>
                            </tr>
                            <tr>
                                <th scope="row">20符（ツモピンフ)</th>
                                <td></td>
                                <td>400/700</td>
                                <td>700/1300</td>
                                <td>1300/2600</td>
                            </tr>
                            <tr>
                                <th scope="row">25符（チートイツ)</th>
                                <td></td>
                                <td>1600</td>
                                <td>3200 (800/1600)</td>
                                <td>6400 (1600/3200)</td>
                            </tr>
                            <tr>
                                <th scope="row">30符</th>
                                <td>1000 (300/500)</td>
                                <td>2000 (500/1000)</td>
                                <td>3900 (1000/2000)</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">40符</th>
                                <td>1300 (400/700)</td>
                                <td>2600 (700/1300)</td>
                                <td>5200 (1300/2600)</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">50符</th>
                                <td>1600 (400/800)</td>
                                <td>3200 (800/1600)</td>
                                <td>6400 (1600/3200)</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">60符</th>
                                <td>2000 (500/1000)</td>
                                <td>3900 (1000/2000)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">70符</th>
                                <td>2300 (600/1200)</td>
                                <td>4500 (1200/2300)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">80符</th>
                                <td>2600 (700/1300)</td>
                                <td>5200 (1300/2600)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">90符</th>
                                <td>2900 (800/1500)</td>
                                <td>5800 (1500/2900)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">100符</th>
                                <td>3200 (800/1600)</td>
                                <td>6400 (1600/3200)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                            <tr>
                                <th scope="row">110符</th>
                                <td></td>
                                <td>7100 (1800/3600)</td>
                                <td>満貫</td>
                                <td>満貫</td>
                            </tr>
                        </tbody></table>
                    </li>
                </ol>
                <h4 class="c-title -dot">第5条 得点の計算および授受</h4>
                <ol class="p-rule__text">
                    <li>得点は必ず、最も高い点に計算する。</li>
                    <li>点棒の授受は、間違いがないように対局者で責任を持って確認し合う。</li>
                    <li>ロンアガリは放銃者が三家分を1人で払い(一家包)、ツモアガリは三家が各自の点数分を払う (三家包)。</li>
                    <li>アガリ点計算及び点棒の授受は、次局の開始前に行うこと。</li>
                    <li>アガリ点の申告はアガリ者がすることを原則とする。</li>
                    <li>ツモアガリの時は、子一人の支払い点、親の支払い点の順に申告し、積み棒がある時は更にそれを加えた点数を申告すること。<br>【例】一本場の時 「1300・2600は1400・2700」</li>
                    <li>点棒の渡し間違いが起きた場合、審判から訂正する。当該半荘が終了するまでは、いかなる状況でも訂正を優先とする。間違えた状態のまま終了した場合は、訂正前の点数を有効とする。</li>
                </ol>
                <h4 class="c-title -dot">第6条 満貫</h4>
                <ol class="p-rule__text">
                    <li>満貫は次の四種とする。（翻数は場ゾロの2翻を含んだもの）<br>
                    （1） 満貫・・・・・・20符以上の7翻、30符以上の6翻と7翻、 60符以上の5翻 。散家 8,000点 荘家 12,000点<br>
                    （2） 跳満・・・・・・20符以上の8翻と9翻 。散家 12,000点 荘家 18,000点<br>
                    （3） 倍満・・・・・・20符以上の10翻から12翻 。散家 16,000点 荘家 24,000点<br>
                    （4） 三倍満・・・・・20符以上の13翻以上。散家 24,000点 荘家 36,000点<br>
                    （5） 四倍満・・・・・役満。散家 32,000点 荘家 48,000点<br>
                    ＊役満の複合はあり。2つの場合（5）の2倍、3つの場合は（5）の3倍とする。</li>
                    <li>役満以外の役が複合したアガリ点は、三倍満までを上限とする。</li>
                </ol>

            </div>
        </section>

        <section class="p-rule__group">
            <h3 class="c-title">
                <span>第7章 罰則</span>
            </h3>
            <div class="p-rule__contents">
                <h4 class="c-title -dot">第1条 罰の種類</h4>
                <ol class="p-rule__text">
                    <li>罰はチョンボ・アガリ放棄の2種とする。</li>
                    <li>1の認定は、いかなるケースに於いても、審判の裁定を優先とする。また対局者からの疑義・指摘はすみやかに行う必要がある。</li>
                </ol>
                <h4 class="c-title -dot">第2条 アガリ放棄とその対象</h4>
                <ol class="p-rule__text">
                    <li>アガリ放棄となった者は、チー・ポン・カン・リーチの権利を失ったまま局の終了まで競技を続行する。<br>
                    これに反した場合はチョンボとなる。</li>
                    <li>以下の行為を対象とする。<br>
                    （1）多牌と少牌<br>
                    ・ 槓のない時の手牌が12枚以下を少牌、14枚以上を多牌という。<br>
                    ・ 多牌・少牌が発覚した者は、アガリ放棄となる。<br>
                    （2）食い換え<br>
                    ・ポン、またはチーの時、副露した対子、または搭子と合わせて面子を構成可能な牌を打することを食い換えと言う。<br>
                    ・食い換えはアガリ放棄とする。<br>
                    （3）空行為<br>
                    ・空行為とは、発声のみで行為を行ってない場合をいう。<br>
                    ・空チー・空ポン・空カン、リーチ発声後の取り消しはアガリ放棄とする。<br>
                    ・ツモ又はロンの発声のみにて、手牌を公開していない場合は、アガリ放棄とする。<br>
                    ・「ツモ」と発声すべき時に「ロン」と発声、またはその逆の発声に関しては審判より『注意』の上、訂正を認める。「ポン・チー・カン」の発声間違えはアガリ放棄とする。<br>
                </li>
                </ol>
                <h4 class="c-title -dot">第3条 錯行為</h4>
                <ol class="p-rule__text">
                    <li>錯チーとは、順子とならないのに順子として副露した場合をいう。その他の錯行為もこれに準ずる。</li>
                    <li>錯チー・錯ポン・錯カンは正しい順子に戻すことが出来れば続行、戻せない場合はアガリ放棄とする。</li>
                    <li>戻せない場合はチー・ポン・カンは非成立とし、指示牌を持ってきた場合は元に戻す。</li>
                </ol>
                <h4 class="c-title -dot">第4条 チョンボとその対象</h4>
                <ol class="p-rule__text">
                    <li>全てのチョンボ該当者は自己のトータルポイントから10ポイントを減算される。（親・子には関わらない）</li>
                    <li>以下の行為をチョンボとする。<br>
                    （1）正当でないアガリを宣言し、手牌を公開した場合はチョンボとなる。<br>
                    （2）ノーテンリーチ及び、リーチ後の不正なカンは、発覚即時、または流局時チョンボとなる。<br>
                    （3）アガリが出た後、点棒授受が完了しない内に山を崩し、アガリ形や裏ドラが不明になった場合は山を崩した当事者がチョンボとなる。<br>
                    （4）牌山及び手牌を故意に崩して公開した、と審判に認められた場合はチョンボとなる。<br>
                    （5）築牌時に山を壊すなど、競技続行を不可能にした場合はチョンボとなる。
                </li>
                    <li>審判に故意と認められたチョンボを行なった場合は、出場資格が問われる</li>
                    <li>チョンボ時に正当なアガリがあれば、その罰を免れる。</li>
                    <li>チョンボ者が複数の場合、全て罰を受ける。</li>
                    <li>チョンボがあった局は、チョンボ者が親・子にかかわらず、その局をやり直す。(積み棒は増えない)</li>
                </ol>

            </div>
        </section>

        <section class="p-rule__group">
            <h3 class="c-title">
                <span>第8章 包 （パオ）</span>
            </h3>
            <div class="p-rule__contents">
                <h4 class="c-title -dot">第1条</h4>
                <p class="p-rule__text">パオの適用を受けた場合、ツモアガったら責任払い、別の放銃者がいたら折半払いとなる。</p>
                <ol class="p-rule__text">
                    <li>パオの適用は、以下の3種類とする。<br>
                        （1）大三元→暗槓並びに副露した三元牌二種がある状態の者に、三種類目の三元牌をポン（大明槓）させた場合。<br>
                        （2）大四喜→暗槓並びに副露した風牌三種がある状態の者に、四種類目の風牌をポン（大明槓）させた場合。<br>
                        （3）四槓子→暗槓並びに明槓を三種ある状態の者に、四種類目を大明カンさせた場合。</li>
                    <li>積み場は鳴かせた者が負担する。</li>
                    <li>アガリ者がダブル役満以上だった場合、まず確定させた役満と積み棒ぶんを鳴かせた者が支払い、その後通常のやりとりをする。<br>
                        （例：一本場で西家が南家の大三元を確定させる牌を鳴かせ、南家の手牌が大三元字一色で、ツモアガった場合　東家16000、西家32300+8000、北家8000　とする）</li>
                </ol>
            </div>
        </section>

        <section class="p-rule__group">
            <h3 class="c-title">
                <span>第9章 アガリ役</span>
            </h3>
            <div class="p-rule__contents">
            <p class="p-rule__text">◎は門前役とする。<br>※は一組でも副露すると一翻下がる。</p>
                <h4 class="c-title -dot">第1条 1翻役</h4>
                <ol class="p-rule__text">
                    <li>門前清自摸和◎</li>
                    <li>立直◎</li>
                    <li>一発◎(一発役は立直に付属し、単独のアガリ役ではない。一発の権利を有する時、槍槓でアガった場合、槍槓時は槓が不成立なので一発と槍槓は複合するが、同じ理由により槓ドラはのらない。)</li>
                    <li>役牌(翻牌)</li>
                    <li>平和◎(ツモアガリの場合は、ツモ符を付けず、20符《連底》計算とし、門前清自摸和と複合する。)</li>
                    <li>断么九</li>
                    <li>一盃口◎</li>
                    <li>海底摸月</li>
                    <li>河底撈魚</li>
                    <li>搶槓</li>
                    <li>嶺上開花</li>
                </ol>

                <h4 class="c-title -dot">第2条 2翻役</h4>
                <ol class="p-rule__text">
                    <li>ダブル立直◎</li>
                    <li>連風牌</li>
                    <li>対々和</li>
                    <li>三暗刻</li>
                    <li>三色同刻</li>
                    <li>三槓子</li>
                    <li>小三元</li>
                    <li>混老頭</li>
                    <li>三色同順※</li>
                    <li>一気通貫※</li>
                    <li>全帯么九※</li>
                    <li>七対子◎(25符とし散家1,600 荘家2,400)</li>
                </ol>
                <h4 class="c-title -dot">第3条 3翻役</h4>
                <ol class="p-rule__text">
                    <li>二盃口◎</li>
                    <li>混一色※</li>
                    <li>純全帯么九※</li>
                </ol>
                <h4 class="c-title -dot">第4条 6翻役</h4>
                <ol class="p-rule__text">
                    <li>清一色※</li>
                </ol>
                <h4 class="c-title -dot">第5条 役満</h4>
                <ol class="p-rule__text">
                    <li>天和◎</li>
                    <li>地和◎</li>
                    <li>国士無双◎</li>
                    <li>四暗刻◎</li>
                    <li>大三元</li>
                    <li>緑一色(緑發が入っていなくてもよい)</li>
                    <li>字一色</li>
                    <li>小四喜</li>
                    <li>大四喜</li>
                    <li>清老頭</li>
                    <li>四槓子</li>
                    <li>九蓮宝燈◎</li>
                </ol>
                <p class="p-rule__text">＊純粋な役満の複合は認められる。</p>

            </div>
        </section>
    </div>
</main>
</body>
</html>

<script>
    const images = document.querySelectorAll('.playerImg img');
    let currentIndex = -1;  // -1から開始

    function showNextImage() {
        currentIndex++;
        if (currentIndex < images.length) {
            images[currentIndex].classList.add('active');
            setTimeout(showNextImage, 1000);
        }
    }

    setTimeout(showNextImage, 1000);
</script>

<style>
    .main {
        padding: 20px;
    }
    .p-rule__body {
        max-width: 960px; /* 全体の最大幅 */
        margin: 0 auto; /* 中央寄せ */
        background-color: #fff;
        padding: 30px;
    }

    /* セクションのグループ */
    .p-rule__group {
        margin-bottom: 40px;
        padding-bottom: 30px;
        border-bottom: 1px dashed #e0e0e0; /* 破線で区切り */
    }

    .p-rule__group:last-child {
        border-bottom: none; /* 最後のセクションには区切りなし */
        margin-bottom: 0;
        padding-bottom: 0;
    }


    /* 節タイトル (h4.c-title -dot) */
    .c-title.-dot {
        padding-top: 22px;
        font-size: 1.9rem;
        text-align: center;
    }

    .c-title.-dot::before {
        content: "";
        display: block;
        position: absolute;
        top: 0;
        left: 50%;
        width: 150px;
        height: 12px;
        border-radius: 6px;
        background-color: #eee;
        -webkit-transform: translateX(-50%);
        transform: translateX(-50%);
    }

    .p-rule__contents .c-title {
        margin: 60px auto 30px;
    }
    /* テキスト段落 */
    .p-rule__text {
        margin-bottom: 1.5em;
        font-size: 1.05em;
        line-height: 1.8;
    }

    .p-rule__text ol {
        margin: 0;
        padding-left: 25px; /* リストのインデント */
    }

    .p-rule__text li {
        margin-bottom: 0.8em;
        position: relative;
    }

    /* リストアイテムの行頭記号をカスタマイズ */
    .p-rule__text ol li::marker {
        color: #5cb85c; /* 緑の番号 */
        font-weight: bold;
    }

    /* テーブルスタイル */
    .p-rule__table {
        width: 100%;
        border-collapse: separate; /* border-spacing を使うために separate に */
        border-spacing: 0; /* ボーダーの間隔をなくす */
        margin: 20px 0;
        font-size: 0.95em;
        border: 1px solid #ddd;
        border-radius: 8px; /* テーブル全体の角丸 */
        overflow: hidden; /* 角丸を適用するためにはみ出しを隠す */
    }

    .p-rule__table th,
    .p-rule__table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
        border-right: 1px solid #eee; /* 右側のボーダー */
    }

    .p-rule__table th {
        background-color: #eef4f9; /* ヘッダーの背景色 */
        color: #444;
        font-weight: bold;
    }

    /* 最後の列の右ボーダーをなくす */
    .p-rule__table th:last-child,
    .p-rule__table td:last-child {
        border-right: none;
    }

    /* 最後の行の下ボーダーをなくす */
    .p-rule__table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ホバーエフェクト */
    .p-rule__table tbody tr:hover {
        background-color: #f9f9f9;
    }

    /* 全幅テーブルの場合の調整 */
    .p-rule__table.-full {
        margin-top: 30px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); /* 少し強めの影 */
    }

    /* 注釈や特別なテキストのスタイル */
    .p-rule__text strong {
        color: #d9534f; /* 強調したい文字は赤 */
    }

    .p-rule__text em {
        font-style: normal;
        background-color: #ffffe0; /* ハイライト */
        padding: 2px 4px;
        border-radius: 3px;
    }

    /* レスポンシブ対応 */
    @media (max-width: 768px) {
        .p-rule__body {
            padding: 15px;
            border-radius: 8px;
        }

        .c-title {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .c-title::before {
            width: 90%;
        }

        .c-title.-dot {
            font-size: 1.1em;
            margin-top: 20px;
        }
        .c-title.-dot::after {
            top: 6px;
            width: 7px;
            height: 7px;
        }
        .p-rule__text {
            font-size: 0.95em;
        }

        .p-rule__table {
            font-size: 0.85em;
            display: block; /* テーブルをブロック要素にして横スクロールを可能にする */
            overflow-x: auto; /* テーブルがはみ出したら横スクロール */
            white-space: nowrap; /* セル内のテキストは改行しない */
        }

        .p-rule__table th,
        .p-rule__table td {
            padding: 8px 10px;
        }
    }
</style>