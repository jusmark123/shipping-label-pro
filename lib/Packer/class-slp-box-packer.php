<?php 

if( ! defined( 'ABSPAth' ) ); exit(); 

if( ! class_exists( 'SLP_Box_Packer' ) ) :

class SLP_Box_Packer {

	private $boxes = array();
	
	private $items = array();
	
	private $boxes = array();
		
	public function add_boxes( $boxes ) {
		
		foreach( $boxes as $key => $box ) {
			$this->boxes[$key] = array(
				'width'  => $box['width'],
				'height' => $box['height'],
				'length' => $box['length'],
				'type'   => $box['type'],
			);	
		}
		
		return $this->boxes;
	}
	
	public function add_item( $item ) {
		$this->items[] = $item;
	}
	
	public function pack_boxes() {
		$boxes = $this->boxes;
	}
}
endif;

