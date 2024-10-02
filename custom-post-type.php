<?php
// Register Custom Post Type for Forms without content area
function create_form_cpt() {
    $labels = array(
        'name'                  => _x( 'Forms', 'Post Type General Name', 'textdomain' ),
        'singular_name'         => _x( 'Form', 'textdomain' ),
        'menu_name'             => __( 'Forms', 'textdomain' ),
        'name_admin_bar'        => __( 'Form', 'textdomain' ),
        'archives'              => __( 'Form Archives', 'textdomain' ),
        'attributes'            => __( 'Form Attributes', 'textdomain' ),
        'all_items'             => __( 'All Forms', 'textdomain' ),
        'add_new_item'          => __( 'Add New Form', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'new_item'              => __( 'New Form', 'textdomain' ),
        'edit_item'             => __( 'Edit Form', 'textdomain' ),
        'update_item'           => __( 'Update Form', 'textdomain' ),
        'view_item'             => __( 'View Form', 'textdomain' ),
        'search_items'          => __( 'Search Form', 'textdomain' ),
        'not_found'             => __( 'Not found', 'textdomain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'textdomain' ),
    );

    $args = array(
        'label'                 => __( 'Form', 'textdomain' ),
        'description'           => __( 'Post type for forms', 'textdomain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'custom-fields' ),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-feedback', 
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type( 'form', $args );
}
add_action( 'init', 'create_form_cpt' );

function add_subtitle_meta_box() {
    add_meta_box(
        'subtitle_meta_box',
        'Question',
        'render_subtitle_meta_box',
        'form',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'add_subtitle_meta_box' );


function render_subtitle_meta_box( $post ) {
    $questions = get_post_meta( $post->ID, '_questions', true ) ?: array();

    $options = array(
        'paragraph'     => 'Paragraph',
        'short_paragraph' => 'Short Paragraph',
        'checkbox'      => 'Checkbox',
        'radio'         => 'Radio',
    );

    echo '<div id="questions-repeater" style="display: flex; flex-direction: column; gap: 20px;">';
    wp_nonce_field( 'save_questions_data', 'questions_nonce' );

    foreach ( $questions as $index => $question ) {
        $subtitle = esc_attr( $question['subtitle'] );
        $field_type = esc_attr( $question['field_type'] );

        echo '<div class="question-item" style="border: 1px solid #ccc; padding: 15px; position: relative;">';
        echo '<a href="#" class="remove-question" style="color: red; position: absolute; top: 5px; right: 5px;">Remove</a>';
        echo '<div style="display: flex; gap: 10px;">';

        echo '<div style="flex: 4;">';
        echo '<label for="subtitle_field_' . $index . '">Question Title:</label>';
        echo '<input type="text" id="subtitle_field_' . $index . '" name="questions[' . $index . '][subtitle]" value="' . $subtitle . '" style="width: 100%;" />';
        echo '</div>';

        echo '<div style="flex: 1;">';
        echo '<label for="field_type_' . $index . '">Field Type:</label>';
        echo '<select id="field_type_' . $index . '" name="questions[' . $index . '][field_type]" class="field_type" style="width: 100%;" data-index="' . $index . '">';
        foreach ( $options as $key => $label ) {
            echo '<option value="' . esc_attr( $key ) . '" ' . selected( $field_type, $key, false ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '</div>';
        echo '</div>';

        echo '<div id="dynamic-fields-' . $index . '" class="dynamic-fields">';

        // Render dynamic fields based on the field type
        if ( $field_type === 'paragraph' ) {
            echo '<label for="paragraph_field_' . $index . '">Paragraph Field:</label>';
            echo '<textarea id="paragraph_field_' . $index . '" name="questions[' . $index . '][paragraph_field]" rows="5" style="width:100%;">' . esc_attr( $question['paragraph_field'] ) . '</textarea>';
        } elseif ( $field_type === 'short_paragraph' ) {
            echo '<label for="short_paragraph_field_' . $index . '">Short Paragraph Field:</label>';
            echo '<input type="text" id="short_paragraph_field_' . $index . '" name="questions[' . $index . '][short_paragraph_field]" value="' . esc_attr( $question['short_paragraph_field'] ) . '" style="width:100%;" />';
        } elseif ( $field_type === 'checkbox' ) {
            $checkbox_values = $question['checkbox_field'] ?: array();
            echo '<label>Checkboxes:</label><br>';
            echo '<input type="checkbox" name="questions[' . $index . '][checkbox_field][]" value="Option 1" ' . (in_array('Option 1', $checkbox_values) ? 'checked' : '') . '> Option 1<br>';
            echo '<input type="checkbox" name="questions[' . $index . '][checkbox_field][]" value="Option 2" ' . (in_array('Option 2', $checkbox_values) ? 'checked' : '') . '> Option 2<br>';
            echo '<input type="checkbox" name="questions[' . $index . '][checkbox_field][]" value="Option 3" ' . (in_array('Option 3', $checkbox_values) ? 'checked' : '') . '> Option 3';
        } elseif ( $field_type === 'radio' ) {
            $radio_value = $question['radio_field'];
            echo '<label>Radio Buttons:</label><br>';
            echo '<input type="radio" name="questions[' . $index . '][radio_field]" value="Option 1" ' . checked('Option 1', $radio_value, false) . '> Option 1<br>';
            echo '<input type="radio" name="questions[' . $index . '][radio_field]" value="Option 2" ' . checked('Option 2', $radio_value, false) . '> Option 2<br>';
            echo '<input type="radio" name="questions[' . $index . '][radio_field]" value="Option 3" ' . checked('Option 3', $radio_value, false) . '> Option 3';
        }

        echo '</div>'; // Close dynamic fields container
        echo '</div>'; // Close question-item div
    }

    echo '</div>'; // Close questions-repeater div

    // Add new question button
    echo '<button type="button" id="add-question" style="margin-top: 10px;">Add New Question</button>';

    ?>
   <script>
document.addEventListener('DOMContentLoaded', function () {
    const addQuestionBtn = document.getElementById('add-question');
    const questionsRepeater = document.getElementById('questions-repeater');
    let questionIndex = <?php echo count($questions); ?>;

    addQuestionBtn.addEventListener('click', function () {
        const newQuestion = `
            <div class="question-item" style="border: 1px solid #ccc; padding: 15px; position: relative;">
                <a href="#" class="remove-question" style="color: red; position: absolute; top: 5px; right: 5px;">Remove</a>
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 4;">
                        <label for="subtitle_field_${questionIndex}">Question Title:</label>
                        <input type="text" id="subtitle_field_${questionIndex}" name="questions[${questionIndex}][subtitle]" style="width: 100%;" />
                    </div>
                    <div style="flex: 1;">
                        <label for="field_type_${questionIndex}">Field Type:</label>
                        <select id="field_type_${questionIndex}" name="questions[${questionIndex}][field_type]" class="field_type" data-index="${questionIndex}" style="width: 100%;">
                            <option value="paragraph">Paragraph</option>
                            <option value="short_paragraph">Short Paragraph</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="radio">Radio</option>
                        </select>
                    </div>
                </div>
                <div id="dynamic-fields-${questionIndex}" class="dynamic-fields"></div>
            </div>
        `;

        questionsRepeater.insertAdjacentHTML('beforeend', newQuestion);
        questionIndex++;
    });

    questionsRepeater.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-question')) {
            e.preventDefault();
            e.target.closest('.question-item').remove();
        }
    });
});
    </script>
    <?php
}


function save_subtitle_meta_box( $post_id ) {
    if ( ! isset( $_POST['questions_nonce'] ) || ! wp_verify_nonce( $_POST['questions_nonce'], 'save_questions_data' ) ) {
        return;
    }

    // Bail out if running an autosave, ajax, or user doesn't have the permission
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $questions = isset( $_POST['questions'] ) ? $_POST['questions'] : array();

    // Loop through and sanitize each question field
    foreach ( $questions as $index => $question ) {
        $questions[ $index ]['subtitle'] = sanitize_text_field( $question['subtitle'] );
        $questions[ $index ]['field_type'] = sanitize_text_field( $question['field_type'] );

        if ( $question['field_type'] === 'paragraph' ) {
            $questions[ $index ]['paragraph_field'] = sanitize_textarea_field( $question['paragraph_field'] );
        } elseif ( $question['field_type'] === 'short_paragraph' ) {
            $questions[ $index ]['short_paragraph_field'] = sanitize_text_field( $question['short_paragraph_field'] );
        } elseif ( $question['field_type'] === 'checkbox' ) {
            $questions[ $index ]['checkbox_field'] = array_map( 'sanitize_text_field', $question['checkbox_field'] );
        } elseif ( $question['field_type'] === 'radio' ) {
            $questions[ $index ]['radio_field'] = sanitize_text_field( $question['radio_field'] );
        }
    }

    update_post_meta( $post_id, '_questions', $questions );
}
add_action( 'save_post', 'save_subtitle_meta_box' );
?>
