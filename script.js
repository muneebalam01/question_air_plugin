let questionCount = 0;

function showAnswer(event, optionId) {
    event.preventDefault();
    questionCount++;

    // Handle "Not Eligible" selection
    if (optionId === 'NE') {
        handleNotEligible();
        return; // Stop further processing for not eligible users
    }

    // If question count reaches 7, show the thank-you message
    if (questionCount >= 7) {
        handleMaxQuestionsReached();
        return; // Stop further question display
    }

    // Handle normal question navigation
    handleQuestionSelection(event, optionId);
}

function handleNotEligible() {
    // Hide all questions
    const allQuestions = document.querySelectorAll('.question');
    allQuestions.forEach(q => q.classList.remove('visible'));

    // Show not eligible message
    document.getElementById('not-eligible-message').style.display = 'block';

    // Log for debugging
    console.log('User marked as not eligible');

    // Save "Not Eligible" answer to the database
    const questionText = "Eligibility"; // You might want to use a proper question here
    const selectedAnswerText = "Not Eligible";
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

function handleQuestionSelection(event, optionId) {
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

    // Get the question and selected answer text
    const questionText = event.target.closest('.question').querySelector('p').textContent.trim();
    const selectedAnswerText = event.target.textContent.trim();

    // Log for debugging
    console.log("Question: " + questionText);
    console.log("Selected answer text is: " + selectedAnswerText);

    // Save the selected answer to the database
    saveAnswer(questionText, selectedAnswerText);
}

function saveAnswer(question, answer) {
    fetch(ajaxurl, { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'save_question_answer', 
            question: question,
            answer: answer,
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
