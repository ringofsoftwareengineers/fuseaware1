<?php

//page scrapper class fetches a page and stores data in a Result list
class PageScrapper {

    public $error = '';
   
    /**

     * @param $url  URL of page to be fetched.
     * @return string String of fetched page
     */
    public function fetch_page($url) {

        try {
            $ch = curl_init();
            // Check if initialization had gone wrong*    
            if ($ch === false) {
                $this->error = "failed to initialize";
                return false;
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $content = curl_exec($ch);
            // Check the return value of curl_exec(), too
            if ($content === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            return $content;
        } catch (Exception $e) {

            $this->error = $e->getMessage();
            return false;
        }
    }

//end function
    public function parseHTML($html_string) {
        $doc = new DOMDocument();
        //disable errors created due to html5 doctype
        libxml_use_internal_errors(true);
        $doc->loadHTML($html_string);
        libxml_clear_errors();
        return $doc;
    }

//end function

    public function getResults(DOMDocument $doc, $categorey) {
        $result_list= new ResultList();
        $articles = $doc->getElementsByTagName("article");  
        $total_size=0.0;
            foreach ($articles as $article) {
                $footer_content=$article->getElementsByTagName("footer")->item(0)->textContent;
                if(!strpos($footer_content, $categorey))
                {
                    //skipp rest of work if categorey does not match
                    continue;
                }
                $header = $article->getElementsByTagName("header")->item(0); 
                
                $heading = $header->getElementsByTagName("h2")->item(0);
                $heading_text = $heading->textContent;                
                $url = $heading->getElementsByTagName("a")->item(0)->getAttribute("href");                 
                $meta = $header->getElementsByTagName("time")->item(0)->textContent;                      
                $file_size = strlen($article->textContent);
                
                $result = new Result();
                $result->meta=$meta;
                $result->link=$heading_text;
                $result->url=$url;
                $result->file_size=round($file_size/1024, 2)."kb";
                
                $result_list->results[]=$result;
                $total_size += round($file_size/1024, 2);
                
                               
            }//end foreach
            if (count($result_list) > 0) {
                $result_list->total_size = $total_size."kb";
            return $result_list;
        }
        return false;
    }

//end function
    
    public function scrap_page($url,$categorey)
    {
        $html_string = $this->fetch_page($url);
        if(!$html_string)
        {
            return false;
        }
        $doc= $this->parseHTML($html_string);
        
        return $this->getResults($doc, $categorey);
        
    }//end function
    
}
