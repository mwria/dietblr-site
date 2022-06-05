var quiz = [{
  "question": "Canela ajuda a controlar o diabetes.",
  "choices": ["mito", "verdade"],
  "correct": "mito"
}, {
  "question": "Não é recomendado ingerir bebidas alcoólicas quando se tem diabetes.",
  "choices": ["Mito", "Verdade"],
  "correct": "Verdade"
}, {
  "question": "Diabético pode consumir mel, açúcar mascavo e caldo de cana sem problemas.",
  "choices": ["Mito", "Verdade"],
  "correct": "Mito"
}, {
  "question": "Estresse ajuda a descontrolar o diabetes.",
  "choices": ["Mito", "Verdade"],
  "correct": "Verdade"
}, {
  "question": "Comer doce leva ao diabetes.",
  "choices": ["Mito", "Verdade"],
  "correct": "Mito"
}];


// define elements
var content = $("content"),
  questionContainer = $("question"),
  choicesContainer = $("choices"),
  scoreContainer = $("score"),
  submitBtn = $("submit");

// init vars
var currentQuestion = 0,
  score = 0,
  askingQuestion = true;

function $(id) { // shortcut for document.getElementById
  return document.getElementById(id);
}

function askQuestion() {
  var choices = quiz[currentQuestion].choices,
    choicesHtml = "";

  // loop through choices, and create radio buttons
  for (var i = 0; i < choices.length; i++) {
    choicesHtml += "<input type='radio' name='quiz" + currentQuestion +
      "' id='choice" + (i + 1) +
      "' value='" + choices[i] + "'>" +
      " <label for='choice" + (i + 1) + "'>" + choices[i] + "</label><br>";
  }

  // load the question
  questionContainer.textContent = "Q" + (currentQuestion + 1) + ". " +
    quiz[currentQuestion].question;

  // load the choices
  choicesContainer.innerHTML = choicesHtml;

  // setup for the first time
  if (currentQuestion === 0) {
    scoreContainer.textContent = "Pontuação: 0 respostas certas de " +
      quiz.length + " possiveis.";
    submitBtn.textContent = "Responder Questão";
  }
}

function checkAnswer() {
  // are we asking a question, or proceeding to next question?
  if (askingQuestion) {
    submitBtn.textContent = "Próxima Questão";
    askingQuestion = false;

    // determine which radio button they clicked
    var userpick,
      correctIndex,
      radios = document.getElementsByName("quiz" + currentQuestion);
    for (var i = 0; i < radios.length; i++) {
      if (radios[i].checked) { // if this radio button is checked
        userpick = radios[i].value;
      }

      // get index of correct answer
      if (radios[i].value == quiz[currentQuestion].correct) {
        correctIndex = i;
      }
    }

    // setup if they got it right, or wrong
    var labelStyle = document.getElementsByTagName("label")[correctIndex].style;
    labelStyle.fontWeight = "bold";
    if (userpick == quiz[currentQuestion].correct) {
      score++;
      labelStyle.color = "green";
    } else {
      labelStyle.color = "red";
    }

    scoreContainer.textContent = "Pontuação: " + score + "  respostas certas de " +
      quiz.length + " possiveis.";
  } else { // move to next question
    // setting up so user can ask a question
    askingQuestion = true;
    // change button text back to "Submit Answer"
    submitBtn.textContent = "Submit Answer";
    // if we're not on last question, increase question number
    if (currentQuestion < quiz.length - 1) {
      currentQuestion++;
      askQuestion();
    } else {
      showFinalResults();
    }
  }
}

function showFinalResults() {
  content.innerHTML = "<h2>Você completou o teste!</h2>" +
    "<h2>Seu resultado é</h2>" +
    "<h2>" + score + " de " + quiz.length + " questões, " +
    Math.round(score / quiz.length * 100) + "%<h2>";
}

window.addEventListener("load", askQuestion, false);
submitBtn.addEventListener("click", checkAnswer, false);
