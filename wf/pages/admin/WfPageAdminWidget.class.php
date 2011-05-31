<?php
/**
 * Description of WfPageAdmin
 *
 * @author павел
 */
class WfPageAdminWidget extends WfPageAdmin {
	
	public function widgetUpdate($request) {
		$widgetForm = new WfWidgetForm('');
		
		echo $this->_twig->render('wf_admin_widget.html', array(
			'widget_form' => $widgetForm->render()
		));
	}
	
}