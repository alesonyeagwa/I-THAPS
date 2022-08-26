<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Front render class for custom forms
 */
class Forminator_QForm_Result extends Forminator_Result {

	protected $post_type = 'quizzes';

	/**
	 * @param Forminator_Form_Entry_Model $entry
	 *
	 * @return string
	 */
	public function get_og_description( $entry ) {
		$description = '';
		$quiz        = Forminator_Quiz_Form_Model::model()->load( $entry->form_id );
		if ( $quiz instanceof Forminator_Quiz_Form_Model ) {
			if ( 'knowledge' === $quiz->quiz_type ) {
				$description = $this->get_og_description_knowledge( $quiz, $entry );
			} else {
				$description = $this->get_og_description_nowrong( $quiz, $entry );
			}
		}

		return $description;
	}

	/**
	 * @param Forminator_Quiz_Form_Model  $quiz
	 * @param Forminator_Form_Entry_Model $entry
	 *
	 * @return string
	 */
	private function get_og_description_knowledge( $quiz, $entry ) {
		$total       = 0;
		$right       = 0;
		$description = '';
		$data        = $entry;
		$entry_id    = $entry->entry_id;
		$quiz_title  = '';

		if ( isset( $quiz->settings['quiz_name'] ) ) {
			$quiz_title = esc_html( $quiz->settings['quiz_name'] );
		}

		if ( isset( $data->meta_data['entry'] ) ) {
			$answers = $data->meta_data['entry']['value'];
			if ( is_array( $answers ) ) {
				$total = count( $answers );
				foreach ( $answers as $key => $answer ) {
					if ( true === $answer['isCorrect'] ) {
						$right ++;
					}
				}
			}

		}

		if ( $total > 0 ) {
			$description = sprintf( __( 'I got %1$s/%2$s on %3$s quiz!', Forminator::DOMAIN ), esc_html( $right ), esc_html( $total ), $quiz_title );
		}

		/**
		 * Filter Meta og:description for Knowledge Quiz Result Page
		 *
		 * @since 1.5.2
		 *
		 * @param string                     $description
		 * @param Forminator_Quiz_Form_Model $quiz
		 * @param int                        $entry_id
		 * @param int                        $right      right answer
		 * @param int                        $total      total answer
		 * @param string                     $quiz_title Quiz name
		 */
		$description = apply_filters( 'forminator_quiz_knowledge_result_page_meta_description', $description, $quiz, $entry_id, $right, $total, $quiz_title );

		return $description;

	}

	/**
	 * @param Forminator_Quiz_Form_Model  $quiz
	 * @param Forminator_Form_Entry_Model $entry
	 *
	 * @return string
	 */
	private function get_og_description_nowrong( $quiz, $entry ) {
		$result_slug  = null;
		$result       = null;
		$description  = '';
		$quiz_title   = '';
		$result_title = '';
		$entry_id     = $entry->entry_id;
		if ( isset( $quiz->settings['quiz_name'] ) ) {
			$quiz_title = esc_html( $quiz->settings['quiz_name'] );
		}

		if ( isset( $entry->meta_data['entry'] ) ) {
			$entry_value = $entry->meta_data['entry']['value'];
			if ( is_array( $entry_value ) && isset( $entry_value[0] ) ) {
				$entry_value = $entry_value[0];


				// its disgusting because of the way we saved it
				if ( isset( $entry_value['value'] ) ) {
					$result_value = $entry_value['value'];

					if ( isset( $result_value['result'] ) && isset( $result_value['result']['slug'] ) && ! empty( $result_value['result']['slug'] ) ) {
						$result_slug = $result_value['result']['slug'];
					}
				}
			}
		}


		if ( ! is_null( $result_slug ) ) {
			$result = $quiz->getResult( $result_slug );
		}


		if ( $result ) {
			if ( isset( $result['title'] ) ) {
				$result_title = esc_html( $result['title'] );
			}
			$description = sprintf( __( 'I got %1$s on %2$s quiz!', Forminator::DOMAIN ), $result_title, $quiz_title );
		}

		/**
		 * Filter Meta og:description for no wrong Quiz Result Page
		 *
		 * @since 1.5.2
		 *
		 * @param string                     $description
		 * @param Forminator_Quiz_Form_Model $quiz
		 * @param int                        $entry_id
		 * @param array                      $result     result detail
		 * @param string                     $quiz_title Quiz name
		 */
		$description = apply_filters( 'forminator_quiz_nowrong_result_page_meta_description', $description, $quiz, $entry_id, $result, $quiz_title );

		return $description;

	}

	public function print_result_header() {
		global $wp;
		$entry_id = $this->entry_id;
		$entry    = new Forminator_Form_Entry_Model( $this->entry_id );
		if ( $entry->entry_type !== $this->post_type ) {

			// not this result
			parent::print_result_header();

		} else {

			$query_args = array();
			$query      = wp_parse_url( $this->post_data['_wp_http_referer'] );
			$permalink  = get_option( 'permalink_structure' );

			if ( empty( $permalink ) && isset( $query['query'] ) ) {
				$query_args = $query['query'];
			}

			$description = $this->get_og_description( $entry );

			$featured_img_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
			ob_start();
			?>
			<meta property="og:url"
			      content="<?php echo esc_html( home_url( add_query_arg( $query_args, $wp->request ) ) ); ?>"/>
			<meta property="og:title" content="<?php single_post_title( '' ); ?>"/>
			<meta property="og:description" content="<?php echo esc_html( $description ); ?>"/>
			<meta property="og:type" content="article"/>
			<?php
			if ( false !== $featured_img_url ) {
				?>
				<meta property="og:image" content="<?php echo esc_html( $featured_img_url ); ?>"/>
				<?php
			}
			$header = ob_get_clean();

			/**
			 * Filter Header for Quiz Result Page
			 *
			 * @since 1.5.2
			 *
			 * @param string $header
			 * @param int    $entry_id
			 */
			$header = apply_filters( 'forminator_quiz_result_page_header', $header, $entry_id );

			echo $header; // WPCS XSS OK.

		}

	}
}
