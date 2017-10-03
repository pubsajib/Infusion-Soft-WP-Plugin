<?php 
/**
* User Interface class
*/
class tn_ui_backend extends tn_db {
	function __construct(){
		// parent::__construct();

	}
    public function backend_url($pageName='teachers', $echo=false){
        if ( $pageName == 'teachers') $url = esc_url( admin_url('?page=tn-'.$pageName) );
        else $url = esc_url( admin_url('admin.php?page=tn-'.$pageName) );
        if ($echo) { echo $url; }
        else { return $url; }
    }
	public function get_teachers_list_table_open(){
		$html = '';
		$html .= '<div class="table-responsive">';
		$html .= '<table class="table table-bordered table-striped" id="list">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th>Sl.</th>';
		$html .= '<th style="min-width:80px;">Name</th>';
		$html .= '<th style="min-width:80px;">Email</th>';
		$html .= '<th style="min-width:80px;">Phone</th>';
		$html .= '<th style="min-width:80px;">Country</th>';
		$html .= '<th style="min-width:80px;">State</th>';
		$html .= '<th style="min-width:80px;">Zip</th>';
		// $html .= '<th style="min-width:80px;">Update Values</th>';
		// $html .= '<th style="width:100px;">View/Click</th>';
		$html .= '<th style="min-width:100px;">Update Status</th>';
		$html .= '<th class="text-center" style="min-width: 100px;">Action</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		return $html;
	}
	public function get_teachers_list_table_close(){
		$html = '';
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';
		return $html;
	}
	public function get_teachers_list_table_actions($ID = ''){
		$html = '';
        $url    = $this->backend_url();
        $nurl    = $this->backend_url('add_new');
        $deleteUrl   .= $url.'&del='.$ID;
        $editUrl   .= $nurl.'&id='.$ID;
        $viewUrl   .= $nurl.'&id='.$ID;
		$html .= '<a href="'.$editUrl.'" class="btn btn-primary action-btn"> <span class="glyphicon glyphicon-edit"></span> </a>';
		// $html .= '<a href="'.$deleteUrl.'" class="btn btn-danger action-btn"> <span class="glyphicon glyphicon-trash"></span> </a>';
		$html .= '<a href="'.$viewUrl.'" class="btn btn-warning action-btn"> <span class="glyphicon glyphicon-eye-open"></span> </a>';
		return $html;
	}

	public function teachers_list_table_update_actions2($ID = ''){
        $html   = '';
        $url    = $this->backend_url();
        $updateUrl   .= $url.'&update_apply='.$ID;
        $rejectUrl   .= $url.'&update_reject='.$ID;
		$html  .= '<a href="'.$updateUrl.'" class="btn btn-primary action-btn"> <span class="glyphicon glyphicon-edit"></span> </a>';
		$html  .= '<a href="'.$rejectUrl.'" class="btn btn-danger action-btn"> <span class="glyphicon glyphicon-trash"></span> </a>';
		return $html;
	}

	public function waiting_update_btn($ID = ''){
        $html   = '';
        $url    = $this->backend_url();
        $updateUrl   .= $url.'&update_apply='.$ID;
		$html  .= '<a href="'.$updateUrl.'" class="btn btn-danger action-btn"> Apply Update </a>';
		return $html;
	}

	public function approved_btn($ID = ''){
        $tag = new tn_tag;
        if ( !$tag->has_must_tag($ID) ) {
			$html   = '';
	        $url    = $this->backend_url('teachers');
	        $updateUrl   .= $url.'&published='.$ID;
			$html  .= '<a href="'.$updateUrl.'" class="btn btn-info action-btn publishBtn" user_id='.$ID.'> Publish </a>';
			return $html;
        } else{
	        $html   = '';
	        $url    = $this->backend_url('add_new');
	        $updateUrl   .= $url.'&id='.$ID;
			$html  .= '<a href="'.$updateUrl.'" class="btn btn-primary action-btn"> Approved </a>';
			return $html;
        }
	}
}