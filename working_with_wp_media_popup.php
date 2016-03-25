<?php 
/*
Function khởi tạo
@params:
    $name: Tên của khối thêm hình
    $attachments: Mảng các id của hình đã chọn
    $multiple: Chọn nhiều file cùng lúc (chưa thử nghiệm)
*/
function tvn_gallery_init( $name, $attachments = array(), $multiple = 1 ) { ?>
	<div class="tvn-gallery-label">
		<label for=""><?php echo $name; ?></label>
		<p class="desc"><small>Ext: jpg, png, jpeg</small></p>
	</div>

	<div class="tvn-gallery-field">
		<div class="tvn-gallery-main">
			<ul class="tvn-gallery-input">
			<?php foreach($attachments as $bnkey => $bn):
				if( wp_attachment_is_image( $bn ) ): ?>
					<li class="tvn-gallery-item">
						<input type="hidden" class="save-image" name="tvn_gallery[]" value="<?php echo intval( $bn ); ?>">
						<div class="item">
							<img src="<?php echo wp_get_attachment_url( $bn ); ?>" alt="">
						</div>
						<a class="cta tvn-gallery-remove-item" href="#remove" title="Remove">x</a>
					</li>
			<?php endif;
			endforeach; ?>
			</ul>
			
			<div class="cta-gallery-toolbar" style="background-color: #fff;">
				<a class="cta button button-primary tvn-gallery-add-item" href="#add-to-gallery" data-multiple="1">Add Images</a>
			</div>
		</div>

		<div class="tvn-gallery-side">
			<div class="tvn-gallery-side-inner">
				<div class="tvn-gallery-side-data"></div>
				<div class="cta-gallery-toolbar" style="background-color: #f1f1f1;">
					<a class="cta button button-primary tvn-gallery-side-submit" href="#submit-side">Update</a>
					<span class="spinner"></span>
					<a class="cta button tvn-gallery-side-close" href="#close-side">Close</a>
				</div>
			</div>
		</div>
	</div>
    
    <style>
    .tvn-gallery-label label{
    	color: #444;
    	font-weight: bold;
    }
    .tvn-gallery-label p.desc{
    	color: #666;
    	font-size: 12px;
    }
    .tvn-gallery-field{
    	display: block;
    	position: relative;
    	width: 100%;
    	min-height: 500px;
    	padding: 0;
    	border: 1px solid #ccc;
    	overflow: hidden;
    }
    .tvn-gallery-main{
    	position: absolute;
    	top: 0; right: 0; bottom: 0; left: 0;
    	background-color: #fff;
    	z-index: 2;
    }
    .tvn-gallery-input{
    	width: 100%;
    	height: 100%;
    	padding: 5px;
    }
    .tvn-gallery-input li{
    	float: left;
    	position: relative;
    	width: 18%;
    	padding: 3px;
    	text-align: center;
    	list-style: none;
    }
    .tvn-gallery-input li:nth-of-type(5n+1){
    	clear: both;
    }
    .tvn-gallery-input li .item{
    	border: 4px solid transparent;
    	border-radius: 2px;
    }
    .tvn-gallery-input li img{
    	display: block;
    	max-width: 100%;
    	width: 100%;
    	height: auto;
    	cursor: pointer;
    }
    .tvn-gallery-input li:hover .item {
    	border: 4px solid #1E8CBE;
    }
    .tvn-gallery-input li .tvn-gallery-remove-item{
    	display: none;
    	position: absolute;
    	top: -3px; right: -10px;
    	z-index: 2;
    	width: 20px;
    	height: 20px;
    	padding: 0;
    	border-radius: 50%;
    	background-color: rgba(0,0,0,0.8);
    	color: #fff;
    	font-size: 10px;
    	text-align: center;
    	text-decoration: none;
    }
    .tvn-gallery-input li:hover .tvn-gallery-remove-item{
    	display: block;
    }
    .cta-gallery-toolbar{
    	position: absolute;
    	right: 0; bottom: 0; left: 0;
    	z-index: 100;
    	height: 28px;
    	padding: 10px;
    	border-top: 1px solid #ccc;
    }
    .tvn-gallery-side{
    	position: absolute;
    	top: 0; right: -309px; bottom: 0;
    	width: 309px;
    	border-left: 1px solid #ccc;
    	background-color: #f1f1f1;
    	-webkit-transition: right 0.3s linear;
    	transition: right 0.3s linear;
    }
    .tvn-gallery-info{
    	padding: 15px 10px;
    	background-color: #e4e4e4;
    }
    .tvn-gallery-info img{
    	float: left;
    	width: 60px;
    	height: 60px;
    	margin-right: 10px;
    }
    .tvn-gallery-info p{
    	color: #666;
    	line-height: 1.25;
    	margin: 0;
    }
    .tvn-gallery-info p.info-name{
    	font-weight: bold;
    }
    .tvn-gallery-side-data table input,
    .tvn-gallery-side-data table textarea{
    	width: 100%;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
    	var file_frame;
    
    	// Sortable init
    	if( $('body').find('.tvn-gallery-input').length > 0 ) {
    		$('.tvn-gallery-input').sortable();
    	}
    
    	// Add images
    	$('.tvn-gallery-add-item').click(function(event) {
    		event.preventDefault();
    
    		var _this = $(this);
    		var _multi = Boolean( parseInt( $(this).data('multiple') ) );
    		var container = _this.parents('.tvn-gallery-main').children('.tvn-gallery-input');
    
    		if ( file_frame ) {
    			file_frame.open();
    			return;
    		}
    
    		file_frame = wp.media.frames.file_frame = wp.media({
                title: _multi ? 'Select images' : 'Select image',
                library: {type: 'image'},
                button: {text: 'Select'}, 
                multiple: _multi
            });
    
            var images_id = $('.tvn-gallery-input .save-image').map( function() { return $(this).val(); } );
            file_frame.on('open', function() {
                var selection = file_frame.state().get('selection');
                $.each( images_id, function(index, el) {
                    var attachment = wp.media.attachment( el );
                    attachment.fetch();
                    selection.add( attachment ? [ attachment ] : [] );
                });
            });
    
            file_frame.on('select', function() {
                attachment = file_frame.state().get('selection');
                if( !_multi ){
                    attachment = attachment.first().toJSON();
                    if(typeof attachment.id != 'undefined'){
                    	$.ajax({
                    		url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    		type: 'POST',
                    		dataType: 'html',
                    		data: {action: 'backend__get_image_url_by_id', id: attachment.id}
                    	})
                    	.done(function( image_url ) {
                            html += '<li class="tvn-gallery-item">';
                            html += '	<input type="hidden" class="save-image" name="tvn_gallery[]" value="'+ attachment.id +'">';
                            html += '	<div class="item">';
                            html += '		<img src="'+ image_url +'" alt="">';
                            html += '	</div>';
                            html += '	<a class="cta tvn-gallery-remove-item" href="#remove" title="Remove">x</a>';
                            html += '</li>';
    
                			container.append( html );
                			container.sortable();
                    	});
                    }
                }
                else{
                    attachment = attachment.toJSON();
                    container.html( '' );
                    $.each(attachment, function(index, el) {
                    	var html = '';
                        if(typeof el.id != 'undefined'){
                        	$.ajax({
                        		url: '<?php echo admin_url("admin-ajax.php"); ?>',
                        		type: 'POST',
                        		dataType: 'html',
                        		data: {action: 'backend__get_image_url_by_id', id: el.id}
                        	})
                        	.done(function( image_url ) {
                                html += '<li class="tvn-gallery-item">';
                                html += '	<input type="hidden" class="save-image" name="tvn_gallery[]" value="'+ el.id +'">';
                                html += '	<div class="item">';
                                html += '		<img src="'+ image_url +'" alt="">';
                                html += '	</div>';
                                html += '	<a class="cta tvn-gallery-remove-item" href="#remove" title="Remove">x</a>';
                                html += '</li>';
    
                    			container.append( html );
                    			container.sortable();
                        	});
                        }
                    });
                }
            });
    
            file_frame.open();
    	});
    
    	// Edit image and attributes
    	$('.tvn-gallery-main').delegate('img', 'click', function(event) {
    		var field = $(this).parents('.tvn-gallery-field');
    		var main = $(this).parents('.tvn-gallery-main');
    		var side = $(field).children('.tvn-gallery-side');
    		var side_data = $(field).find('.tvn-gallery-side-data');
    		var img_id = $(this).parents('li').children('input.save-image').val();
    
    		$.ajax({
    			url: '<?php echo admin_url("admin-ajax.php"); ?>',
    			type: 'POST',
    			dataType: 'html',
    			data: {action: 'backend__get_image_data_by_id', id: img_id}
    		})
    		.done(function( html ) {
    			side_data.html( html );
    
    			main.animate({'right': '310px'}, 300);
    			side.animate({'right': '0'}, 300);
    		});
    
    	});
    
    	// Remove images
    	$('.tvn-gallery-main').delegate('.tvn-gallery-remove-item', 'click', function(event) {
    		event.preventDefault();
    		if( confirm( 'Can not rollback! Are you sure?' ) )
    			$(this).parents('li').remove();
    
    		return false;
    	});
    
    	// Submit side
    	$('.tvn-gallery-side-submit').click(function(event) {
    		event.preventDefault();
    		var spinner = $(this).siblings('.spinner');
    		$(spinner).addClass('is-active');
    
    		$.ajax({
    			url: '<?php echo admin_url("admin-ajax.php"); ?>',
    			type: 'POST',
    			dataType: 'json',
    			data: {
    				action: 'backend__save_image_data',
    				id: $('.tvn-side-id').val(),
    				title: $('.tvn-side-title').val(),
    				url: $('.tvn-side-url').val(),
    				desc: $('.tvn-side-desc').val()
    			}
    		})
    		.done(function( response ) {
    			$(spinner).removeClass('is-active');
    		});
    	});
    
    	// Close side_data
    	$('.tvn-gallery-side-close').click(function(event) {
    		event.preventDefault();
    		var field = $(this).parents('.tvn-gallery-field');
    		var main = $(field).children('.tvn-gallery-main');
    		var side = $(field).children('.tvn-gallery-side');
    
    		main.animate({'right': '0'}, 300);
    		side.animate({'right': '-310px'}, 300);
    	});
    });
    </script>
<?php }

// Lấy src của ảnh theo id và size
function wp_get_image_src($id, $size = 'full'){
    if($id > 0){
        $imageInfo = wp_get_attachment_image_src($id, $size);
        return $imageInfo[0];
    }
    return false;
}

// Ajax - phục vụ cho các thao tác trên pick gallery
add_action( 'wp_ajax_backend__get_image_url_by_id', 'backend__get_image_url_by_id_cb' );
function backend__get_image_url_by_id_cb() {
	$id = intval( $_POST['id'] );

	if( 'attachment' == get_post_type( $id ) )
		echo wp_get_image_src( $id, 'large' );

	exit;
}

// Ajax - phục vụ cho các thao tác trên pick gallery
add_action( 'wp_ajax_backend__get_image_data_by_id', 'backend__get_image_data_by_id_cb' );
function backend__get_image_data_by_id_cb() {
	$id = intval( $_POST['id'] );
	$html = '';

	if( 'attachment' == get_post_type( $id ) ) {
		$data = get_post( $id );
		$metadata = wp_get_attachment_metadata( $id );

		$title = get_the_title( $id );
		$desc = $data->post_content;
		// $slug = $data->post_name;
		$caption = $data->post_excerpt;
		$filename = basename( $data->guid );
		$date = get_the_time( 'd/m/Y', $id );
		$size = $metadata['width'] .'x'. $metadata['height'];

		$html .= '<div class="tvn-gallery-info">';
		$html .= '<input type="hidden" class="tvn-side-id" value="'. $id .'">';
		$html .= '<img src="'. wp_get_image_src( $id, 'thumbnail' ) .'" alt="">';
		$html .= '<p class="info-name">'. $filename .'</p>';
		$html .= '<p class="info-date">'. $date .'</p>';
		$html .= '<p class="info-dimension">'. $size .'</p>';
		$html .= '<div class="clear"></div>';
		$html .= '</div>';
		$html .= '<table class="form-table">';
		$html .= '<tbody>';
		$html .= '<tr>';
		$html .= '<td>Title</td>';
		$html .= '<td><input type="text" class="tvn-side-title" name="tvn_attachment['. $id .'][title]" id="" value="'. $title .'"></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>URL</td>';
		$html .= '<td><input type="text" class="tvn-side-url" name="tvn_attachment['. $id .'][url]" id="" value="'. $caption .'"></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>Description</td>';
		$html .= '<td><textarea class="tvn-side-desc" name="tvn_attachment['. $id .'][desc]" id="">'. $desc .'</textarea></td>';
		$html .= '</tr>';
		/*$html .= '<tr>';
		$html .= '<td>Alt Text</td>';
		$html .= '<td><input type="text" name="tvn_attachment['. $id .'][alt]" id="" value="'. $slug .'"></td>';
		$html .= '</tr>';*/
		$html .= '</tbody>';
		$html .= '</table>';

		echo $html;
	}
	
	exit;
}

// Ajax - phục vụ cho các thao tác trên pick gallery
add_action( 'wp_ajax_backend__save_image_data', 'backend__save_image_data_cb' );
function backend__save_image_data_cb() {
	$id = intval( $_POST['id'] );
	$title = sanitize_text_field( $_POST['title'] );
	$url = sanitize_text_field( $_POST['url'] );
	$desc = sanitize_text_field( $_POST['desc'] );

	wp_update_post( array(
		'ID' => $id,
		'post_title' => $title,
		'post_excerpt' => $url,
		'post_content' => $desc,
	) );

	echo json_encode( array( 'status' => 1 ) );
	exit;
}
?>
