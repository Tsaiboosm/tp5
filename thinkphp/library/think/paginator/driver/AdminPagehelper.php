<?php

namespace think\paginator\driver;
use think\Paginator;

class AdminPagehelper extends Paginator {
    public $btnCount = 7; // 按钮数（必须是奇数），否则会不对称
    
    /**
     * 渲染分页html
     *
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '<ul class=pagination>%s %s %s</ul>',
                    $this->getPreviousButton(),
                    $this->getNextButton()
                    );
            } else {
                return sprintf(
                    '<ul class=pagination>%s</ul>',
                    $this->getLinks()
                    );
            }
        }
    }
    
    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {
        if ($this->lastPage <= $this->btnCount) {
            //return  $this->getUrlRange(1, $this->lastPage);
            return $this->getPreviousButton() .
                    $this->getUrlLinks($this->getUrlRange(1, $this->lastPage)) .
                    $this->getNextButton();
        }

        $cur = $this->currentPage;
        $half = floor($this->btnCount / 2);
        
        if ($cur > $half && $cur <= ($this->lastPage - $half)) {
            $sta = $cur - $half;
            $end = $cur + $half;
        } else if ($cur <= $half) {
            $sta = 1;
            $end = $this->btnCount;
        } else if ($cur > ($this->lastPage - $half)) {
            $sta = $this->lastPage - $this->btnCount + 1;
            $end = $this->lastPage;
        }
        
        //var_dump($sta);
        //var_dump($end);exit();
 
        return $this->getFirstButton() .
                $this->getPreviousButton() .
                $this->getUrlLinks($this->getUrlRange($sta, $end)) .
                $this->getNextButton() .
                $this->getLastButton();
    }
    
    /**
     * 批量生产页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';
        
        foreach ($urls as $page => $url) {
            if ($page != $this->currentPage) {
                $html .= $this->getAvailablePageWrapper($url, $page);
            } else {
                $html .= $this->getCurrentPageWrapper();
            }
        }
        
        return $html;
    }
    
    /**
     * 首页按钮
     *
     * @param string $text
     * @return string
     */
    protected function getFirstButton($text = '首页')
    {
        if ($this->currentPage > ceil($this->btnCount / 2)) {
            
            $url = $this->url(1);
            
            return $this->getAvailablePageWrapper($url, $text);
        }
    }
    
    /**
     * 末页按钮
     *
     * @param string $text
     * @return string
     */
    protected function getLastButton($text = '末页')
    {
        $half = floor($this->btnCount / 2);
        if ($this->currentPage < ($this->lastPage - $half)) {
            
            $url = $this->url($this->lastPage);
            
            return $this->getAvailablePageWrapper($url, $text);
        }
    }
    
    
    /**
     * 上一页按钮
     *
     * @param string $text
     * @return string
     */
    protected function getPreviousButton($text = "上一页")
    {
        
        if ($this->currentPage() <= 1) {
            //return $this->getDisabledTextWrapper($text);
            // 当当前页为1时，不需要“上一页”的按钮
            return '';
        }
        
        $url = $this->url(
            $this->currentPage() - 1
            );
        
        return $this->getAvailablePageWrapper($url, $text);
    }
    
    /**
     * 下一页按钮
     *
     * @param string $text
     * @return string
     */
    protected function getNextButton($text = "下一页")
    {
        if (!$this->hasMore) {
            //return $this->getDisabledTextWrapper($text);
            return '';
        }
        
        $url = $this->url($this->currentPage() + 1);
        
        return $this->getAvailablePageWrapper($url, $text);
    }
    
    /**
     * 生成一个可点击的按钮
     *
     * @param string $url
     * @param int  $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return '<li><a href="' . htmlentities($url) . '" rel="external nofollow">' . $page . '</a></li>';
    }
    
    /**
     * 生成一个当前页的按钮
     *
     * @param string $text
     * @return string
     */
    protected function getCurrentPageWrapper()
    {
        //return '<span>' . $this->currentPage . '</span>';
        return "<li class='active disabled'><a>" . $this->currentPage . '</a></li>';
    }

}

?>
