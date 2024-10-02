function showAnswer(option, nextQuestionId) {
    // Hide current question and all answers
    document.querySelectorAll('.question').forEach(function(question) {
        question.classList.remove('visible');
    });
    document.querySelectorAll('.answer').forEach(function(answer) {
        answer.style.display = 'none';
    });

    // Show the next question based on the selected answer
    document.getElementById(nextQuestionId).classList.add('visible');
}

function showFinalAnswer(finalAnswerId) {
    // Hide all questions
    document.querySelectorAll('.question').forEach(function(question) {
        question.classList.remove('visible');
    });

    // Show the final answer based on the option selected
    document.getElementById('answer' + finalAnswerId).style.display = 'block';
}
