<?php
namespace App\Http\Services;

use App\Models\Category;
use App\Models\News;
use Goutte\Client;

class ScrapService{
    private $url = "https://dantri.com.vn/";

    public function scrapCategory(){
        $client = new Client();
        $crawler = $client->request('GET', $this->url);
        $crawler->filter('.clearfix ul li a')->each(function ($node, $i) {
            if($i>=2){
                Category::create(['name' => $node->text(), 'parent_id' => 1, 'active' => 1]);
                $this->scrapNews($this->url.$node->attr('href'), $i);
            }
        });

    }

    public function scrapNews($link, $category_id){
        $client = new Client();
        $crawler = $client->request('GET', $link);
        $crawler->filter('div.mt3.clearfix')->each(function ($news, $j) use(&$category_id) {
            if($j != 0) {
                $title = $news->filter('.mr1 h2 a')->text();
                $short_description = $news->filter('.fon5.fl')->text();
                $image = $news->filter('a img')->attr('src');
                $linkDetail = $this->url.$news->filter('.mr1 h2 a')->attr('href');
                $client = new Client();
                $crawerDescription = $client->request('GET', $linkDetail);
                $description = $crawerDescription->filter("#divNewsContent")->html();

                News::create([
                    'title' => $title,
                    'short_description' => $short_description,
                    'category_id' => $category_id,
                    'image' => $image,
                    'description' => $description,
                ]);
            }

        });
    }
}
