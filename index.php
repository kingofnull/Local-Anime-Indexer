<?php
include __DIR__."/core.php";  
$sort=@$_GET['sort'];
$order=@$_GET['order'];
if(!$sort)$sort='rank';
if(!$order)$order='asc';

$alBaseUrl="https://animelist.eu/search?type=anime&s=";

$list=$db->animelist()->order("$sort $order");
?><!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>AnimList Local-<?=date("Y/m/d")?></title>
		<link rel="icon" type="image/ico" href="favicon.ico" />
		
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Place favicon.ico in the root directory -->
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
		
		
		<div id="content-wrapper">
			<div id="header">
				<div>
					<label>Search: </label>
					<input id="query">
				</div>
				<div>
					<label>Sort	: </label>
					<a href="?sort=es_score&order=desc">ES-Score</a>
					<a href="?sort=rank&order=asc">Rank</a>
					<a href="?sort=popularity&order=asc">Popularity</a>
					<a href="?sort=members&order=desc">Members</a>
					<a href="?sort=added_time&order=asc">Added Time</a>
				</div>
			</div>
			<div id="items-grid">
				<?php foreach($list as $a):
					if(!file_exists("$a[path]") || $a['title']==""){
							continue;
					}
				?>
				<div class="item-wrapper">
					<img class="thumb" src="<?="thumbnails/$a[real_id].jpg"?>" "display:inline-block"/>
					<div class="data-wrapper">
						<div class="title" href="<?="$a[url]"?>" target="_blank"><?=$a['title']?></div>
						
						
						<div class="extra-data">
							<?php if($a['sec_title']!=$a['title']):?>
							<div class="sec-title" target="_blank" href="https://animelist.eu/search?type=anime&s=<?=$a['title']?>"><?=$a['sec_title']?></div>
							
							<?php endif;?>
							
							<div class="data-item year">Year: <span class="year-value value"><?=$a['year']?></span></div>
							<div class="data-item score">Score: 
								<div class="score-v-wrapper">
									<div class="score-bar" style="width:<?=$a['score']*10?>%">
										<span class="score-value">
										<?=$a['score']?>
										</span>
									</div>
									
								</div>
							</div>
							<div class="data-item ">Rank: <span class="value">#<?=$a['rank']?></span></div>
							<div class="data-item ">Members: <span class="value"><?=number_format($a['members'])?></span></div>
							<div class="data-item ">Popularity: <span class="value"><?=$a['popularity']?></span></div>
							<div class="data-item ">ES-Score: <span class="value"><?=round($a['es_score'],2)?></span></div>
							<div class="data-item ">Episodes: <span class="value"><?=$a['episodes']?></span></div>
							<div class="data-item ">Added: <span class="value"><?=explode(' ',$a['added_time'])[0]?></span></div>
							<div class="data-item ">Geners: <span class="value"><?=$a['genres']?></span></div>
							<a class="data-item path" path="<?="$a[path]"?>" href="<?=str_replace("\\","/","explore://$a[path]")?>" ><?=$a['path']?></a>
					
						</div>
					</div>
					<div class="ex-links">
						<a class="mal-link" href="<?="$a[url]"?>" target="_blank">MAL</a>
						<span ></span>
						<a class="al-link"  href="<?=$alBaseUrl.$a['title']?>" target="_blank">AL</a>
					</div>
				</div>
				<?php endforeach;?>
			</div>
		</div>


        <script src="jquery-3.4.1.js"></script>
        <script>
			function copyToClipboard(text) {
			  var $temp = $("<input>");
			  $("body").append($temp);
			  $temp.val(text).select();
			  document.execCommand("copy");
			  $temp.remove();
			}
			
			$(".path").click(function(){
				copyToClipboard(this.innerText);
			})
			
			jQuery.expr[':'].icontains = function(a, i, m) {
			  return jQuery(a).text().toUpperCase()
				  .indexOf(m[3].toUpperCase()) >= 0;
			}
			
            $("#query").on("input",function(){
				$(".item-wrapper").hide().filter(`:icontains(${this.value})`).show();
			})
			
			
			$("[href]").each(function() {
				if (this.href == window.location.href) {
					$(this).addClass("active");
				}
			});
			
			
        </script>
    </body>
</html>
