<?php
/*
Plugin Name: IWS Question Answer (TSP)
Plugin URI: https://techsolutionspro.co.uk/
Description:  IWS Question Answer Plugin developed by TSP shortcode is iws_display_questions.
Version: 1.2
Author: Tech Solution Pro
Author URI: https://techsolutionspro.co.uk/
Network: true
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
        'iws_display_questions_in_admin' 
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
    $dropdown_options = array(2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 'NE');
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
                            <select name="iws_question_airs[<?php echo $index; ?>][option1_id]" class="next_question_id">
                                <option value="">Select Next Question</option>
                                <?php foreach ($questions as $key => $q) : ?>
                                    <?php if ($key !== $index) : // Skip current question ?>
                                    <option value="<?php echo $key + 1; ?>" <?php selected($question['option1_id'], $key); ?>>
                                        <?php echo esc_html($q['question']); // Display the question title ?>
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <option value="NE" <?php selected($question['option1_id'], 'NE'); ?>>Not Eligible</option>
                            </select>
                        </div>
                        <div class="option_flex_cls">
                        <input type="text" name="iws_question_airs[<?php echo $index; ?>][option2]" value="<?php echo esc_attr($question['option2']); ?>" placeholder="Option 2" />
                            <select name="iws_question_airs[<?php echo $index; ?>][option2_id]" class="next_question_id">
                                <option value="">Select Next Question</option>
                                <?php foreach ($questions as $key => $q) : ?>
                                    <?php if ($key !== $index) : // Skip current question ?>
                                    <option value="<?php echo $key; ?>" <?php selected($question['option2_id'], $key); ?>>
                                        <?php echo esc_html($q['question']); // Display the question title ?>
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <option value="NE" <?php selected($question['option2_id'], 'NE'); ?>>Not Eligible</option>
                            </select>
                        </div>
                        <div class="option_flex_cls">
                        <input type="text" name="iws_question_airs[<?php echo $index; ?>][option3]" value="<?php echo esc_attr($question['option3']); ?>" placeholder="Option 3" />
                            <select name="iws_question_airs[<?php echo $index; ?>][option3_id]" class="next_question_id">
                                <option value="">Select Next Question</option>
                                <?php foreach ($questions as $key => $q) : ?>
                                    <?php if ($key !== $index) : // Skip current question ?>
                                    <option value="<?php echo $key; ?>" <?php selected($question['option3_id'], $key); ?>>
                                        <?php echo esc_html($q['question']); // Display the question title ?>
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <option value="NE" <?php selected($question['option3_id'], 'NE'); ?>>Not Eligible</option>
                            </select>
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
                        <div class="option_flex_cls">
                            <select name="iws_question_airs[${newIndex}][option1]">
                                <option value="">Select Next Question</option>
                                ${[...Array(newIndex)].map((_, i) => `<option value="${i}">Question ${i + 1}</option>`).join('')}
                                <option value="NE">Not Eligible</option>
                            </select>
                        </div>
                        <div class="option_flex_cls">
                            <select name="iws_question_airs[${newIndex}][option2]">
                                <option value="">Select Next Question</option>
                                ${[...Array(newIndex)].map((_, i) => `<option value="${i}">Question ${i + 1}</option>`).join('')}
                                <option value="NE">Not Eligible</option>
                            </select>
                        </div>
                        <div class="option_flex_cls">
                            <select name="iws_question_airs[${newIndex}][option3]">
                                <option value="">Select Next Question</option>
                                ${[...Array(newIndex)].map((_, i) => `<option value="${i}">Question ${i + 1}</option>`).join('')}
                                <option value="NE">Not Eligible</option>
                            </select>
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

        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            echo '<form>';
            echo '<div class = "welcome_message_user"style="padding:5px;
            /* border: 1px solid gray;
             */border-radius: 5px;
             margin-bottom: 40px;
             box-shadow: 4px 6px 7px 10px rgba(8, 8, 4, 0.1);
             width:650px"><h2>Welcome, ' . esc_html($current_user->user_login) . '!</h2></div>';

            foreach ($questions as $index => $question) {
                $question_id = 'question' . ($index + 1);
                $options_id = 'options' . ($index + 1);
                $visible_class = ($index === 0) ? 'visible' : '';
                
                $option_one = is_numeric($question['option1_id']) ? $question['option1_id'] + 1 : 'NE';
                $option_two = is_numeric($question['option2_id']) ? $question['option2_id'] + 1 : 'NE';
                $option_three = is_numeric($question['option3_id']) ? $question['option3_id'] + 1 : 'NE';

                ?>

                <div id="<?php echo esc_attr($question_id); ?>" class="question <?php echo esc_attr($visible_class); ?>">
                    <p><?php echo esc_html($question['question']); ?></p>

                    <button class="radio-button"
                            onclick="showAnswer(event, '<?php echo ($option_one == 'NE') ? 'NE' : $option_one; ?>')"
                            id='<?php echo esc_html($question['option1']); ?>'>
                          
                        <?php echo esc_html($question['option1']); ?>
                    </button>

                    <button class="radio-button"
                            onclick="showAnswer(event, '<?php echo ($option_two == 'NE') ? 'NE' : $option_two; ?>')"
                            id='<?php echo esc_html($question['option2']); ?>'>
                        <?php echo esc_html($question['option2']); ?>
                    </button>

                    <button class="radio-button"
                            onclick="showAnswer(event, '<?php echo ($option_three == 'NE') ? 'NE' : $option_three; ?>')"
                            id='<?php echo esc_html($question['option3']); ?>'>
                        <?php echo esc_html($question['option3']); ?>
                    </button>
                    <?php
                    ?>
                </div>
                <?php
            }

            echo '<div id="thank-you-message" class="thank_you_after_questions" style="display:none;">
                    <h2>Thank you for completing the questions!</h2>
                  </div>';

            echo '<div id="not-eligible-message" class="not_eligible_message" style="display:none;">
                  <h2>Sorry, you are not eligible for this package!</h2>
                    </div>
                    ';

            echo '</form>';
        } else {
            $current_url = esc_url(home_url(add_query_arg(null, null)));
            echo '<div class="login_register_first" style="padding:5px">
                    <h2>Welcome, please Login/signup for the questionnaire!</h2>
                    <a href="' . wp_login_url($current_url) . '" style="font-size: 24px; font-weight: 600;">
                    Click here to login</a>
                  </div>';
        }
    }

    return ob_get_clean();
}

add_shortcode('iws_display_questions', 'iws_display_questions');


function iws_display_questions_in_admin() {
    $data = get_option('question_answers', []);

    if (isset($_POST['delete_question']) && check_admin_referer('iws_delete_question_nonce')) {
        $question_to_delete = sanitize_text_field($_POST['delete_question']);
        
        foreach ($data as $key => $entry) {
            if ($entry['question'] === $question_to_delete) {
                unset($data[$key]);
                break;
            }
        }
        update_option('question_answers', array_values($data)); 
    }
    ?>
    <div class="widefat">
        <h1>Question and Answer Results</h1>
        <?php if (!empty($data)): ?>
            <table class="form-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>View</th>
                        <th>Eligibilty</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                   
                    $grouped_data = [];
                    foreach ($data as $entry) {
                        $grouped_data[$entry['username']][] = $entry;
                    }

                    foreach ($grouped_data as $username => $entries): ?>
                        <tr>
                            <td><?php echo esc_html($username); ?></td>
                            <td>
                                <button class="view-questions-button" data-username="<?php echo esc_attr($username); ?>">View</button>
                            </td>
                            <td>

                            <?php
                                
                                $eligible = true; 
                                foreach ($entries as $entry) {
                                    if ($entry['answer'] === 'NE') {
                                        $eligible = false; 
                                        break;
                                    }
                                }
                                echo $eligible ? 'Eligible' : 'Not Eligible';
                                ?>
                                
                            </td>
                            <td>
                                <form method="post">
                                    <?php wp_nonce_field('iws_delete_question_nonce'); ?>
                                    <input type="hidden" name="delete_question" value="<?php echo esc_attr($entries[0]['question']); ?>" />
                                    <input type="submit" value="Delete" class="button button-danger" />
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div id="question-modal" style="display:none;">
                <div class="modal-content">
                    <h2>User Questions and Answers</h2>
                    <div id="question-modal-content"></div>
                    <button id="close-modal">Close</button>
                </div>
            </div>
        <?php else: ?>
            <p>No questions answered yet.</p>
        <?php endif; ?>
    </div>
    <?php
}
function iws_admin_styles_questions() {
    echo '<style>
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
    #question-modal {
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    </style>';
}
add_action('admin_head', 'iws_admin_styles_questions');



add_action('wp_ajax_iws_fetch_user_questions', 'iws_fetch_user_questions_callback');

function iws_fetch_user_questions_callback() {
    if (isset($_POST['username'])) {
        $username = sanitize_text_field($_POST['username']);
        $data = get_option('question_answers', []);
        
        // Filter questions for the specified user
        $user_questions = array_filter($data, function($entry) use ($username) {
            return $entry['username'] === $username;
        });

        if (!empty($user_questions)) {
            $response = '';
            foreach ($user_questions as $question) {
                $response .= '<p><strong>Question:</strong> ' . esc_html($question['question']) . '<br>';
                $response .= '<strong>Answer:</strong> ' . esc_html($question['answer']) . '</p>';
            }
            wp_send_json_success($response);
        } else {
            wp_send_json_error('No questions found for this user.');
        }
    } else {
        wp_send_json_error('Invalid username.');
    }
}



function iws_admin_scripts() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.view-questions-button').click(function() {
            var username = $(this).data('username');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'iws_fetch_user_questions',
                    username: username
                },
                success: function(response) {
                    if (response.success) {
                        $('#question-modal-content').html(response.data);
                        $('#question-modal').show();
                    } else {
                        alert(response.data);
                    }
                }
            });
        });

        $('#close-modal').click(function() {
            $('#question-modal').hide();
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'iws_admin_scripts');





add_action('wp_ajax_save_question_answer', 'save_question_answer_callback');
add_action('wp_ajax_nopriv_save_question_answer', 'save_question_answer_callback');

function save_question_answer_callback() {
    if (isset($_POST['question']) && isset($_POST['answer'])) {
        $question = sanitize_text_field($_POST['question']);
        $answer = sanitize_text_field($_POST['answer']);
        $user = wp_get_current_user();
        $username = is_user_logged_in() ? $user->user_login : 'Guest';
        $data = get_option('question_answers', []);
        $data[] = [
            'question' => $question,
            'answer' => $answer,
            'username' => $username,
        ];
        update_option('question_answers', $data);
        wp_send_json_success(['question' => $question, 'answer' => $answer, 'username' => $username]);
    } else {
        wp_send_json_error('Invalid data');
    }
}
