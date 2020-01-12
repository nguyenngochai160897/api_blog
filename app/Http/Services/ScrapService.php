<?php
namespace App\Http\Services;

use App\Models\Category;
use App\Models\News;
use Goutte\Client;

class ScrapService{
    private $url = "https://dantri.com.vn";

    public function scrapCategory(){
        $client = new Client();
        $crawler = $client->request('GET', $this->url);
        $crawler->filter('.clearfix ul.nav li a')->each(function ($node, $i) {
            if($i>=3){
                Category::create(['name' => $node->text(), 'parent_id' => 1, 'active' => 1]);
                $this->scrapNews($this->url.$node->attr('href'), $i-1);
            }
        });

    }

    public function scrapNews($link, $category_id){
        $client = new Client();
        $crawler = $client->request('GET', $link);
        $crawler->filter('div.mt3.clearfix.eplcheck')->each(function ($news, $j) use(&$category_id) {
            try {
                $news->filter('.ul_submenu li a')->each(function($category_child) use(&$category_id){
                    Category::create(['name' => $category_child->text(), 'parent_id' => ($category_id), 'active' => 1]);
                });

                $title = $news->filter('.mr1 h2 a')->eq(0)->text();
                $short_description = $news->filter('.fon5.fl')->eq(0)->text();
                $image = $news->filter('a img')->eq(0)->attr('src');
                $linkDetail = $this->url.$news->filter('.mr1 h2 a')->eq(0)->attr('href');
                $client = new Client();
                $crawerDescription = $client->request('GET', $linkDetail);
                $description = $crawerDescription->filter("#divNewsContent.detail-content")->html();

                News::create([
                    'title' => $title,
                    'short_description' => $short_description,
                    'category_id' => $category_id,
                    'image' => $image,
                    'description' => $description,
                    'active' => true
                ]);
                }
            catch (\Throwable $th) {
                echo $category_id."<br>";
                echo $th->getMessage();
            }

        });
    }
}
