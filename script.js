let questionCount = 0;

function showAnswer(event, optionId) {
    event.preventDefault();
    questionCount++;

    const questionElement = event.target.closest('.question');
    const questionText = questionElement.querySelector('p').textContent.trim();
    const selectedAnswerText = optionId === 'NE' ? "Not Eligible" : event.target.textContent.trim();

    // Handle "Not Eligible" selection
    if (optionId === 'NE') {
        handleNotEligible(questionText, selectedAnswerText);
        return; // Stop further processing for not eligible users
    }

    // If question count reaches 7, show the thank-you message
    if (questionCount >= 7) {
        handleMaxQuestionsReached();
        return; // Stop further question display
    }

    // Handle normal question navigation
    handleQuestionSelection(event, optionId, questionText, selectedAnswerText);
}

function handleNotEligible(questionText, selectedAnswerText) {
    // Hide all questions
    const allQuestions = document.querySelectorAll('.question');
    allQuestions.forEach(q => q.classList.remove('visible'));

    // Show not eligible message
    document.getElementById('not-eligible-message').style.display = 'block';

    // Log for debugging
    console.log(`${questionText}: ${selectedAnswerText}`);

    // Save the question and "Not Eligible" answer to the database
    saveAnswer(questionText, selectedAnswerText);
}

function handleMaxQuestionsReached() {
    // Hide all questions
    document.querySelectorAll('.question').forEach(question => {
        question.style.display = 'none';
    });

    // Show the thank you message
    document.getElementById('thank-you-message').style.display = 'block';
}

function handleQuestionSelection(event, optionId, questionText, selectedAnswerText) {
    // Deselect previous answer buttons
    const buttons = document.querySelectorAll('.radio-button');
    buttons.forEach(btn => btn.classList.remove('selected'));

    // Highlight selected answer
    event.target.classList.add('selected');

    // Hide all questions and show the next one
    const allQuestions = document.querySelectorAll('.question');
    allQuestions.forEach(q => q.classList.remove('visible'));

    const nextQuestion = document.getElementById('question' + optionId);
    if (nextQuestion) {
        nextQuestion.classList.add('visible');
    }

    // Log for debugging
    console.log(`Question: ${questionText}`);
    console.log(`Selected answer: ${selectedAnswerText}`);

    // Save the question and selected answer to the database
    saveAnswer(questionText, selectedAnswerText);
}

function saveAnswer(question, answer) {
    fetch(iws_ajax_object.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'save_question_answer',
            question: question,
            answer: answer,
            _ajax_nonce: iws_ajax_object.nonce // Security nonce
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Data saved successfully:', data.data);
        } else {
            console.error('Error saving data:', data.data);
        }
    })
    .catch(error => console.error('Error:', error));
}

function showFinalAnswer(finalAnswerId) {
    // Hide all questions and display the final answer
    document.querySelectorAll('.question').forEach(question => {
        question.classList.remove('visible');
    });
    document.getElementById(`answer${finalAnswerId}`).style.display = 'block';
}








function submitAnswers(username, answers) {
    fetch('https://your-site.com/wp-json/iws/v1/submit-answers', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: username,
            answers: answers
        }),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log('Success:', data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
