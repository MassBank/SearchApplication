<?php

class All_Search_Param extends Abstract_Search_Param
{
	private $_keyword_terms;
	private $_mol_mass;
	private $_mol_mass_tolerance;
	private $_formula_term;
	
	private $_peak_mz;
	private $_peak_mz_tolerance;
	private $_peak_mz_diff;
	private $_peak_mz_cutoff;
	
	public function get_keyword_terms(){
		return $this->_keyword_terms;
	}
	
	public function set_keyword_terms($_keyword_terms){
		$this->_keyword_terms = $_keyword_terms;
	}
	
	public function get_mol_mass(){
		return $this->_mol_mass;
	}
	
	public function set_mol_mass($_mol_mass){
		$this->_mol_mass = $_mol_mass;
	}
	
	public function get_mol_mass_tolerance(){
		return $this->_mol_mass_tolerance;
	}
	
	public function set_mol_mass_tolerance($_mol_mass_tolerance){
		$this->_mol_mass_tolerance = $_mol_mass_tolerance;
	}
	
	public function get_formula_term(){
		return $this->_formula_term;
	}
	
	public function set_formula_term($_formula_term){
		$this->_formula_term = $_formula_term;
	}
	
	public function get_peak_mz(){
		return $this->_peak_mz;
	}
	
	public function set_peak_mz($_peak_mz){
		$this->_peak_mz = $_peak_mz;
	}
	
	public function get_peak_mz_tolerance(){
		return $this->_peak_mz_tolerance;
	}
	
	public function set_peak_mz_tolerance($_peak_mz_tolerance){
		$this->_peak_mz_tolerance = $_peak_mz_tolerance;
	}
	
	public function get_peak_mz_diff(){
		return $this->_peak_mz_diff;
	}
	
	public function set_peak_mz_diff($_peak_mz_diff){
		$this->_peak_mz_diff = $_peak_mz_diff;
	}
	
	public function get_peak_mz_cutoff(){
		return $this->_peak_mz_cutoff;
	}
	
	public function set_peak_mz_cutoff($_peak_mz_cutoff){
		$this->_peak_mz_cutoff = $_peak_mz_cutoff;
	}
	
}
?>