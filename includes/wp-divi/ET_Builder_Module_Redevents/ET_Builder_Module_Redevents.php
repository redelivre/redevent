<?php

class ET_Builder_Module_Redevents_Search extends ET_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Formulário de Busca de Eventos', 'et_builder' );
		$this->slug = 'et_pb_search_re';

		$this->whitelisted_fields = array(
			'background_layout',
			'text_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'max_width_tablet',
			'max_width_phone',
			'button_text',
		);

		$this->fields_defaults = array(
			'background_layout' => array( 'light' ),
			'background_color'  => array( et_builder_accent_color(), 'add_default_setting' ),
			'text_orientation'  => array( 'left' ),
			'button_text' => array( 'Busca' ),
		);

		$this->main_css_element = '%%order_class%%';
		$this->advanced_options = array(
			'fonts' => array(
				'text'   => array(
					'label'    => esc_html__( 'Text', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'main' => $this->main_css_element,
					),
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'background_layout' => array(
				'label'             => esc_html__( 'Text Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'description'       => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_text_orientation_options(),
				'description'       => esc_html__( 'This controls the how your text is aligned within the module.', 'et_builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can create the content that will be used within the module.', 'et_builder' ),
			),
			'max_width' => array(
				'label'           => esc_html__( 'Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'mobile_options'  => true,
				'tab_slug'        => 'advanced',
				'validate_unit'   => true,
			),
			'max_width_tablet' => array(
				'type' => 'skip',
			),
			'max_width_phone' => array(
				'type' => 'skip',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired button text.', 'et_builder' ),
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id            = $this->shortcode_atts['module_id'];
		$module_class         = $this->shortcode_atts['module_class'];
		$background_layout    = $this->shortcode_atts['background_layout'];
		$text_orientation     = $this->shortcode_atts['text_orientation'];
		$max_width            = $this->shortcode_atts['max_width'];
		$max_width_tablet     = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone      = $this->shortcode_atts['max_width_phone'];
		$button_text       = $this->shortcode_atts['button_text'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$this->shortcode_content = et_builder_replace_code_content_entities( $this->shortcode_content );

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_tablet,
				'phone'   => $max_width_phone,
			);

			et_pb_generate_responsive_css( $max_width_values, '%%order_class%%', 'max-width', $function_name );
		}

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

		$output = sprintf(
			'<div%3$s class="et_pb_text%2$s%4$s">
				%1$s
			</div> <!-- .et_pb_text -->',
			$this->shortcode_content,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		wp_enqueue_style('ui-datepicker', plugin_dir_url( __FILE__ ) . 'css/jquery-ui-1.8.9.custom.css');
		wp_enqueue_style('ui-datepicker', plugin_dir_url( __FILE__ ) . 'css/tf-functions.css');

		wp_enqueue_script('jquery-ui', plugin_dir_url( __FILE__ ) . 'js/jquery-ui-1.8.9.custom.min.js', array('jquery'));
		wp_enqueue_script('ui-datepicker', plugin_dir_url( __FILE__ ) . 'js/jquery.ui.datepicker.js');
		wp_enqueue_script('custom_script', plugin_dir_url( __FILE__ ) .'js/pubforce-admin.js', array('jquery'));

		var_dump($_POST);
		$day = "";
		$month = "";
		$year = "";
		if (array_key_exists("event", $_POST)) {
			$string_date = $_POST["date"];
			list($day, $month , $year) = explode("/",$string_date);
		}
		$city = "";
		if (array_key_exists("city",$_POST)) {
			$city = $_POST["city"];
		}
		$state = "";
		if (array_key_exists("state",$_POST)) {
			$state = $_POST["state"];
		}
		$event = "";
		if (array_key_exists("event",$_POST)) {
			$event = $_POST["event"];
		}
		$output .= '<form method="post" >';
		 	$output .= '<p>';
			$output .= 'Busca: <input type="text" name="event" value="'.$day.'">';
			$output .= '</p>';

			$output .= '<p>Data: ';
			$output .= '<input class="tfdate" name="date" placeholder="DD/MM/YYYY" value="'.$string_date.'" />';
			$output .= '</p>';

			$output .= '<p> Cidade: ';
			$output .= '<input name="city" placeholder="nome ou parte do nome de uma cidade" value="'.$city.'"/>';
			$output .= '</p>';


			$state_labels = array("Acre",
													"Alagoas",
													"Amapá",
													"Amazonas",
													"Bahia",
													"Ceará",
													"Distrito Federal",
													"Espírito Santo",
													"Goiás",
													"Maranhão",
													"Mato Grosso",
													"Mato Grosso do Sul",
													"Minas Gerais",
													"Pará",
													"Paraíba",
													"Paraná",
													"Pernambuco",
													"Piauí",
													"Rio de Janeiro",
													"Rio Grande do Norte",
													"Rio Grande do Sul",
													"Rondônia",
													"Roraima",
													"Santa Catarina",
													"São Paulo",
													"Sergipe",
													"Tocantins");

			$output .= '<p>Estado: ';
			$output .= '<select name="state">';
			foreach ($state_labels as $state_data) {
				$output .= '<option '.(($state==$state_data)?"selected":"").' value="'.$state_data.'">'.$state_data.'</option>';
			}
			$output .= '</select>';
	 		$output .= '</p>';

	 		$output .= '<p>';
	 		$output .= '<button class="et_pb_button" style="background-color:#545454; color: white ">' . ('' !== $button_text ? esc_html( $button_text ) : 'Busca') . '</button>';
	 		$output .= '</p>';
		$output .= '</form>';

		$date = date(strtotime("120 days"));
		$today = date('Y-m-d H:i:s', strtotime("now"));

		$date2 = date(mktime(0, 0, 0, 5, 8, 2017));

		$args = array(
			"post_type" => "tf_events",
			'meta_query' => array(
			    'relation' => 'OR',
		        array(
		            'key'       => 'tf_events_startdate',
		            'value'     => $date,
		            'compare'   => '<=',
		        ),
				array(
		            'key'       => 'tf_events_city',
		            'value'     => 'Campinas',
		            'compare'   => 'LIKE',
		        ),
				array(
		            'key'       => 'tf_events_state',
		            'value'     => 'São Paulo',
		            'compare'   => 'LIKE',
		        ),
		    ),
		);
		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {
			$output .= "<br>";
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$output .= '<p><a href="'.get_permalink().'">' . get_the_title() . '</a></p>';
				$custom_fields = get_post_custom();

		    foreach ( $custom_fields as $key => $value ) {
		      if($key == "tf_events_startdate" or $key == "tf_events_enddate"){
		        $value[0] = date("d-m-Y H:i", $value[0]);
		      }

		      if($key == "tf_events_startdate")
		        $key = "Data de Inicio";

		      if ($key == "tf_events_enddate") {
		        $key = "Data de Termino";
		      }

		      if ($key == "tf_events_city") {
		        $key = "Cidade";
		      }

		      if ($key == "tf_events_state") {
		        $key = "Estado";
		      }
		        if ($key == "_edit_last" or $key == "_edit_lock" or $key == "upload_file") {
		        continue;
		      }

		       $output .= "<label>" . $key . "</label>" . ": " . $value[0] . "<br />";
		    }
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		} else {
			// no posts found
		}
		return $output;
	}
}
new ET_Builder_Module_Redevents_Search;
