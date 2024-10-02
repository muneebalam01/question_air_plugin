function showAnswer(event, optionId) {
    event.preventDefault();

    const buttons = document.querySelectorAll('.radio-button');
    buttons.forEach(btn => {
        btn.classList.remove('selected');
    });

    event.target.classList.add('selected');
    const allQuestions = document.querySelectorAll('.question');
    allQuestions.forEach(q => q.classList.remove('visible'));
    const nextQuestion = document.getElementById('question' + optionId);
    if (nextQuestion) {
        nextQuestion.classList.add('visible');
    }

    const questionText = event.target.closest('.question').querySelector('p').textContent.trim();
    const selectedAnswerText = event.target.textContent.trim();

    console.log("Question: " + questionText);
    console.log("Selected answer text is: " + selectedAnswerText);

    fetch(ajaxurl, { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'save_question_answer', 
            question: questionText,
            answer: selectedAnswerText,
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
    document.querySelectorAll('.question').forEach(question => {
        question.classList.remove('visible');
    });
    document.getElementById(`answer${finalAnswerId}`).style.display = 'block';
}










