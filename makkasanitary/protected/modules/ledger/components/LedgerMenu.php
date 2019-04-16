<?php

class LedgerMenu extends CWidget {

    public function run() {
        $ledgerHeads = new LedgerHead();
        $menuitems = $ledgerHeads->findAll();
        $this->render('ledgermenu', array('menuitems' => $menuitems));
    }

}
