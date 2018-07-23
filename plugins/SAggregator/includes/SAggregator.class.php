<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class SAggre {

    private $cfg;
    private $db;
    private $tpl;
    private $tUtil;
    private $LNG;
    private $ctgs;
    private $sm;
    private $cats;

    public function __construct($cfg, $LNG, $tpl, $db, $tUtil, $ctgs, $sm) {
        $this->cfg = $cfg;
        $this->LNG = $LNG;
        $this->tpl = $tpl;
        $this->db = $db;
        $this->tUtil = $tUtil;
        $this->ctgs = $ctgs;
        $this->cats = null;
        $this->sm = $sm;
    }

    public function getBlock($class, $cat = "all", $limit = 10) {
        $allrsr = $this->getResources($class, $cat, $limit);
        $rt = null;
        foreach ($allrsr as $rsr) {
            $rsr['timeDiff'] = $this->timeFormat($rsr);
            $rsr['catName'] = $this->ctgs->getCatNameByID($rsr['category']);
            $rsr['authorName'] = $this->sm->getUsernameByID($rsr['author_id']);

            if ($this->cfg['SAGGRED_ALLOW_COMM'] ) {
                $rsr['NUM_COMM'] = SC_GetNumComm("SAggregator", $rsr['ag_id'], $this->cfg['WEB_LANG']);
            }
            
            $rt .= $this->tpl->getTPL_file("SAggregator", "block_image", $rsr);
        }
        //$cat_opt = $this->getSelectCats();

        return $rt;
    }

    private function getSelectCats() {
        $cats = $this->ctgs->root_cats("SAggregator", "option");
        $html = "<select>";

        if (!empty($cats)) {
            foreach ($cats as $key => $cat) {
                $html .= "<option>{$cat['name']}</option>";
            }
        }
        $html .= "</select>";

        return $html;
    }

    private function timeFormat($rsr) {
        $rTimeDiff = null;
        $timeDiff = $this->tUtil->timeNowDiff($rsr['created']);
        if ($timeDiff['days'] > 0) {
            $rTimeDiff .= $timeDiff['days'] . " {$this->LNG['DAYS']} ";
            if ($timeDiff['hours'] > 0) {
                $rTimeDiff .= $timeDiff['hours'] . " {$this->LNG['HOURS']} {$this->LNG['AGO']} ";
            } else {
                $rTimeDiff .= " {$this->LNG['AGO']} ";
            }
        } else {
            if ($timeDiff['hours'] > 0) {
                $rTimeDiff .= $timeDiff['hours'] . " {$this->LNG['HOURS']} ";
            }
            $rTimeDiff .= $timeDiff['minutes'] . " {$this->LNG['MINUTES']} {$this->LNG['AGO']} ";
        }
        return $rTimeDiff;
    }

    private function getResources($class, $cat, $limit) {
        $query = $this->db->select_all("aggregator", [ "class" => $class]);

        return $this->db->fetch_all($query);
    }

}
