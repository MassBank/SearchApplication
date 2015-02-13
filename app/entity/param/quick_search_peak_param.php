<?php
class Quick_Search_Peak_Param extends Abstract_Search_Param
{
	/* "quick=true&CEILING=1000&WEIGHT=SQUARE&NORM=SQRT&START=1&TOLUNIT=unit"
				 + "&CORTYPE=COSINE&FLOOR=0&NUMTHRESHOLD=3&CORTHRESHOLD=0.8&TOLERANCE=0.3"
				 + "&CUTOFF=" + pCutoff + "&NUM=0&VAL=" + paramPeak.toString();
				 */
	private $_val 	= "";
	private $_start 	= 1;
	private $_num		= 0;
	private $_floor		= 1;
	private $_celing	= 1000;
	private $_threshold	= 3;
	private $_cutoff	= 20;
	private $_tolerance	= 0.3;
	private $_col_type	= "COSINE";
	private $_weight	= Constant::PARAM_WEIGHT_SQUARE;
	private $_norm		= Constant::PARAM_NORM_SQRT;
	private $_tol_unit	= "unit";
	private $_precursor	= 0;
	private $_ion_mode	= 1;
	
	public function get_val(){
		return $this->_val;
	}
	
	public function set_val($_val){
		if (isset($_val)) {
			$this->_val = $_val;
		}
	}
	
	public function get_start(){
		return $this->_start;
	}
	
	public function set_start($_start){
		if (isset($_start)) {
			$this->_start = $_start;
		}
	}
	
	public function get_num(){
		return $this->_num;
	}
	
	public function set_num($_num){
		if (isset($_num)) {
			$this->_num = $_num;
		}
	}
	
	public function get_floor(){
		return $this->_floor;
	}
	
	public function set_floor($_floor){
		if (isset($_floor)) {
			$this->_floor = $_floor;
		}
	}
	
	public function get_celing(){
		return $this->_celing;
	}
	
	public function set_celing($_celing){
		if (isset($_celing)) {
			$this->_celing = $_celing;
		}
	}
	
	public function get_threshold(){
		return $this->_threshold;
	}
	
	public function set_threshold($_threshold){
		if (isset($_threshold)) {
			$this->_threshold = $_threshold;
		}
	}
	
	public function get_cutoff(){
		return $this->_cutoff;
	}
	
	public function set_cutoff($_cutoff){
		if (isset($_cutoff)) {
			$this->_cutoff = $_cutoff;
		}
	}
	
	public function get_tolerance(){
		return $this->_tolerance;
	}
	
	public function set_tolerance($_tolerance){
		if (isset($_tolerance)) {
			$this->_tolerance = $_tolerance;
		}
	}
	
	public function get_col_type(){
		return $this->_colType;
	}
	
	public function set_col_type($_col_type){
		if (isset($_col_type)) {
			$this->_col_type = $_col_type;
		}
	}
	
	public function get_weight(){
		return $this->_weight;
	}
	
	public function set_weight($_weight){
		if (isset($_weight)) {
			$this->_weight = $_weight;
		}
	}
	
	public function get_norm(){
		return $this->_norm;
	}
	
	public function set_norm($_norm){
		if (isset($_norm)) {
			$this->_norm = $_norm;
		}
	}
	
	public function get_tol_unit(){
		return $this->_tol_unit;
	}
	
	public function set_tol_unit($_tol_unit){
		if (isset($_tol_unit)) {
			$this->_tol_unit = $_tol_unit;
		}
	}
	
	public function get_precursor(){
		return $this->_precursor;
	}
	
	public function set_precursor($_precursor){
		if (isset($_precursor)) {
			$this->_precursor = $_precursor;
		}
	}
	
	public function get_ion_mode(){
		return $this->_ion_mode;
	}
	
	public function set_ion_mode($_ion_mode){
		if (isset($_ion_mode)) {
			$this->_ion_mode = $_ion_mode;
		}
	}
	
}
?>