<?php

namespace Cbx\Phpfpdf;


class Hooks
{

	public function __construct()
	{
		$this->update_checker();
	}

	public function update_checker()
	{
		$updater = new PDUpdater(CBXPHPFPDF_ROOT_PATH . 'cbxphpfpdf.php');
		$updater->set_username('codeboxrcodehub');
		$updater->set_repository('cbxphpfpdf');
		$updater->authorize('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');
		$updater->initialize();

		return;
	}//end method update_checker
}//end class Hooks