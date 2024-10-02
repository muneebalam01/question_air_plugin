<?php
/*
Plugin Name: IWS Question Answer (TSP)
Plugin URI: https://techsolutionspro.co.uk/
Description:  IWS Question Answer Plugin developed by TSP.
Version: 1.2
Author: Tech Solution Pro
Author URI: https://techsolutionspro.co.uk/
*/


function iws_enqueue_scripts() {
    wp_enqueue_style('uk-trivia-game-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('uk-trivia-game-script', plugin_dir_url(__FILE__) . 'script.js', array(), null, true);
    wp_localize_script('uk-trivia-game-script', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'iws_enqueue_scripts');


function iws_add_question_airs_menu() {
    
    add_menu_page(
        'Question Airs', 
        'Question Airs', 
        'manage_options', 
        'question-airs',  
        'iws_question_airs_page_callback', 
        'dashicons-list-view', 
        20 
    );

    add_submenu_page(
        'question-airs', 
        'Answers',       
        'Answers',       
        'manage_options', 
        'question-airs-answers', 
        'iws_answers_page_callback' 
    );
}
add_action('admin_menu', 'iws_add_question_airs_menu');

function iws_question_airs_page_callback() {
    ?>
    <div class="wrap">
        <h1>Question Airs</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('iws_question_airs_group');
            do_settings_sections('question-airs');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
function iws_answers_page_callback() {
    ?>
    <div class="wrap">
        <h1>Answers</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('iws_answers_group');
            do_settings_sections('question-airs-answers');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
function iws_register_question_airs_settings() {
    register_setting('iws_question_airs_group', 'iws_question_airs');
    
    add_settings_section(
        'iws_question_airs_section',
        'Add Questions and Options',
        null,
        'question-airs'
    );
    add_settings_field(
        'iws_question_airs_field',
        null,
        'iws_question_airs_field_callback',
        'question-airs',
        'iws_question_airs_section'
    );
}
add_action('admin_init', 'iws_register_question_airs_settings');
function iws_question_airs_field_callback() {
    $questions = get_option('iws_question_airs', array());
    ?>
    <div id="iws-repeater">
        <?php if (!empty($questions)) : ?>
            <?php foreach ($questions as $index => $question) : ?>
                <div class="iws-repeater-item">
                    <div class="question-container">
                        <input type="text" name="iws_question_airs[<?php echo $index; ?>][question]" value="<?php echo esc_attr($question['question']); ?>" placeholder="Enter Question" />
                        <button type="button" class="remove-question">Remove</button>
                    </div>
                    <div class="options-container">
                    <div class="option_flex_cls">
                         <input type="text" name="iws_question_airs[<?php echo $index; ?>][option1]" value="<?php echo esc_attr($question['option1']); ?>" placeholder="Option 1" />
                        <input type="text" name="iws_question_airs[<?php echo $index; ?>][option1_id]" value="<?php echo esc_attr($question['option1_id']); ?>" placeholder="Option 1 ID" class ="next_question_id"/>
                    </div>
                    <div class="option_flex_cls">
                        <input type="text" name="iws_question_airs[<?php echo $index; ?>][option2]" value="<?php echo esc_attr($question['option2']); ?>" placeholder="Option 2" />
                        <input type="text" name="iws_question_airs[<?php echo $index; ?>][option2_id]" value="<?php echo esc_attr($question['option2_id']); ?>" placeholder="Option 2 ID" class ="next_question_id" />
                    </div>
                    <div class="option_flex_cls">
                        <input type="text" name="iws_question_airs[<?php echo $index; ?>][option3]" value="<?php echo esc_attr($question['option3']); ?>" placeholder="Option 3" />
                        <input type="text" name="iws_question_airs[<?php echo $index; ?>][option3_id]" value="<?php echo esc_attr($question['option3_id']); ?>" placeholder="Option 3 ID"  class ="next_question_id"/>
                    </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button type="button" id="add-question">Add Question</button>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let repeater = document.getElementById('iws-repeater');
        let addButton = document.getElementById('add-question');

        addButton.addEventListener('click', function() {
            let newIndex = repeater.children.length;
            let newItem = `
                <div class="iws-repeater-item">
                    <div class="question-container">
                        <input type="text" name="iws_question_airs[${newIndex}][question]" placeholder="Enter Question" />
                        <button type="button" class="remove-question">Remove</button>
                    </div>
                    <div class="options-container">
                        <div class = "option_1">
                        <input type="text" name="iws_question_airs[${newIndex}][option1]" placeholder="Option 1" />
                        <input type="text" name="iws_question_airs[${newIndex}][option1_id]" placeholder="Next Id" class ="next_question_id" />
                         </div>
                       <div class = "option_1"> 
                       <input type="text" name="iws_question_airs[${newIndex}][option2]" placeholder="Option 2" />
                        <input type="text" name="iws_question_airs[${newIndex}][option2_id]" placeholder="Next Id" class ="next_question_id" />
                        </div>
                        <div class = "option_1">
                        <input type="text" name="iws_question_airs[${newIndex}][option3]" placeholder="Option 3" />
                        <input type="text" name="iws_question_airs[${newIndex}][option3_id]" placeholder="Next Id"  class ="next_question_id"/>
                        </div>
                    </div>
                </div>
            `;
            repeater.insertAdjacentHTML('beforeend', newItem);
        });
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-question')) {
                e.target.parentElement.parentElement.remove();
            }
        });
    });
    </script>

    <style>
        .iws-repeater-item {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 25px 20px 25px 20px;
            background-color: #b2a9a926;
        }

        .question-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px; 
        }

        .options-container input {
            display: block; 
            margin-bottom: 5px; 
        }
        button.remove-question {
            margin-left: 10px; 
        }
        .options-container {
            width: 50%;
            padding-top: 25px;
            gap:10px;
        }
        input[type="text"] {
            width: 80%;
            line-height:40px;
        }
        button.remove-question {
            background-color: #2271b1;
            color: #fff;
            padding: 8px 12px 8px 12px;
            border: none;
            border-radius: 5px;
        }
        input.next_question_id {
        width: 15%;
       }
       .option_flex_cls {
        display: flex;
        }
        .option_1{
            display: flex;
        }
    </style>
    <?php
}

function iws_display_questions() {
  
    $questions = get_option('iws_question_airs', array());
    ob_start(); 
       
    if (!empty($questions)) {
             
         if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            echo  '<form>' ;
       echo '<div style ="padding:5px"><h2>Welcome, ' . esc_html( $current_user->user_login ) . '!</h2></div>'; 
        foreach ($questions as $index => $question) {
            $question_id = 'question' . ($index + 1); 
            $options_id = 'options' . ($index + 1); 
            $visible_class = ($index === 0) ? 'visible' : '';

            ?>
              
            <div id="<?php echo esc_attr($question_id); ?>" class="question <?php echo esc_attr($visible_class); ?>">
            <p><?php echo esc_html($question['question']); ?></p>
               
                <button class="radio-button" onclick="showAnswer(event ,'<?php echo esc_attr($question['option1_id']); ?>')" id ='<?php echo esc_attr($options_id); ?>'>
                    <?php echo esc_html($question['option1']); ?>
                </button>
                <button class="radio-button" onclick="showAnswer(event ,'<?php echo esc_attr($question['option2_id']); ?>')" id ='<?php echo esc_attr($options_id); ?>'>
                    <?php echo esc_html($question['option2']); ?>
                </button>
                <button class="radio-button" onclick="showAnswer(event ,'<?php echo esc_attr($question['option3_id']); ?>')" id ='<?php echo esc_attr($options_id); ?>'>
                    <?php echo esc_html($question['option3']); ?>
                </button>
              
            </div>
            <?php   
        }
        echo  '</form>' ;
    }
     else {
        echo '<div style ="padding:5px"><h2>Welcome, visitor please signup for the question air! </div>';
    } 
    }

    return ob_get_clean(); 
}
add_shortcode('iws_display_questions', 'iws_display_questions');

function iws_admin_styles() {
    echo '<style>
        .wrap h1 {
            margin-bottom: 20px;
        }
        .widefat {
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .widefat th, .widefat td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .widefat th {
            background-color: #f1f1f1;
        }
    </style>';
}
add_action('admin_head', 'iws_admin_styles');

function iws_admin_menu() {
    add_menu_page(
        'Question and Answers',
        'Questions',
        'manage_options',
        'question-airs-answers',
        'iws_display_questions_in_admin',
        'dashicons-format-chat',
        20
    );
}
add_action('admin_menu', 'iws_admin_menu');



function iws_display_questions_in_admin() {
    $data = get_option('question_answers', []); 
    
    ?>
    <div class="widefat">
        <h1>Question and Answer Results</h1>
        <?php if (!empty($data)): ?>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Selected Answer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $question => $answer): ?>
                        <tr>
                            <td><?php echo esc_html($question); ?></td>
                            <td><?php echo esc_html($answer); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No questions answered yet.</p>
        <?php endif; ?>
    </div>
    <?php
}
function iws_admin_styles_questions() {
    echo '<style>
    .wrap h1 {
        margin-bottom: 20px;
    }
    .widefat {
        margin-bottom: 20px;
        border-collapse: collapse;
    }
    .widefat th, .widefat td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .widefat th {
        background-color: #f1f1f1;
    }
</style>';
}
add_action('admin_head', 'iws_admin_styles_questions');

add_action('wp_ajax_save_question_answer', 'save_question_answer_callback');
add_action('wp_ajax_nopriv_save_question_answer', 'save_question_answer_callback');

function save_question_answer_callback() {
    if (isset($_POST['question']) && isset($_POST['answer'])) {
        $question = sanitize_text_field($_POST['question']);
        $answer = sanitize_text_field($_POST['answer']);
        $data = get_option('question_answers', []);
        $data[$question] = $answer;
        update_option('question_answers', $data);
        wp_send_json_success(['question' => $question, 'answer' => $answer]);
    } else {
        wp_send_json_error('Invalid data');
    }
}







