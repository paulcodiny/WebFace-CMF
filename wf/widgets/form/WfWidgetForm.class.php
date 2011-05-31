<?php
/**
 * Description of WfWidgetForm
 *
 * @author павел
 */
class WfWidgetForm extends WfWidget {
	
	protected $_name = 'form';
	
	/**
	 * form options
	 * array(field => array(type:, title:, is_required:, value:
	 */
	
	protected $_options = array();
	protected $_form = array();
	protected $_fields = array();
	
	/**
	 * @param array $options Array of options
	 */
	public function __construct($options, $additionalTemplatePaths = array()) {
		parent::__construct($options, $additionalTemplatePaths = array(
			'wf/widgets/'.$this->_name.'/templates/fields'
		));
		
		$this->_options = json_decode($options, true);
		
		$this->_options = array(
			'fields' => array(
				'title' => array(
					'type' => 'text',
					'title' => 'Название сайта',
					'value' => 'First site on Web2Face',
					'is_required' => true
				),
				'description' => array(
					'type' => 'textarea',
					'title' => 'Мета описание',
					'value' => 'Hello all',
					'is_required' => false
				),
				'keywords' => array(
					'type' => 'textarea',
					'title' => 'Мета ключевые слова',
					'value' => 'Hello all, web2face, demo',
					'is_required' => false
				),
			),
			'form' => array(
				'action' => '',
				'method' => 'post'
			)
		);
		
		$this->_form   = $this->_options['form'];
		$this->_fields = $this->_options['fields'];
		foreach ($this->_fields as $fieldName => &$field) {
			$field['name'] = $fieldName;
		}
	}
	
	public function render() {
		$fieldsHtml = '';
		foreach ($this->_fields as $field) {
			$fieldsHtml .= $this->_twig->render($field['type'] . '.html', array('field' => $field));
		}
		return $this->_twig->render('form.html', array(
			'fieldsHtml' => $fieldsHtml,
			'form' => $this->_form
		));
	}
	
	public function install() {
	
	}
}