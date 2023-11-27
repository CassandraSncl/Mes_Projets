// On initialise la grille du jeu
const grille = ["", "", "", "", "", "", "", "", ""];

// On définit le joueur actuel et l'état du jeu
let joueurActuel;
let jeuFini = false;

// On initialise les scores
let playerScore = 0;
let computerScore = 0;
let matchNuls = 0;

// On vérifie si un joueur a gagné en examinant les lignes gagnantes
function estGagnant(joueur) {
  const lignesGagnantes = [
    [0, 1, 2], [3, 4, 5], [6, 7, 8],
    [0, 3, 6], [1, 4, 7], [2, 5, 8],
    [0, 4, 8], [2, 4, 6]
  ];

  for (const ligne of lignesGagnantes) {
    const [a, b, c] = ligne;
    // On vérifie si les trois cases de la ligne sont occupées par le même joueur
    if (grille[a] === joueur && grille[b] === joueur && grille[c] === joueur) {
      return true; // Le joueur a gagné
    }
  }
  return false; // Aucun joueur n'a gagné sur aucune ligne
}

// On gère le coup joué par un joueur
function jouerCoup(empl, joueur) {
  // On vérifie si le jeu n'est pas fini, la case est vide, et c'est le tour du joueur actuel
  if (!jeuFini && grille[empl] === "" && joueurActuel === joueur) {
    const symbole = joueur === 'humain' ? 'X' : 'O';
    grille[empl] = symbole;
    $(`#${empl}`).addClass(joueur);

    // On vérifie s'il y a une victoire après avoir joué le coup
    if (estGagnant(symbole)) {
      setTimeout(function () {
        alert(`Le joueur ${joueur} a gagné !`);
        // On met à jour les scores et réinitialise le jeu
        joueur === 'humain' ? playerScore++ : computerScore++;
        document.getElementById(joueur === 'humain' ? 'playerScore' : 'computerScore').textContent = joueur === 'humain' ? playerScore : computerScore;
        jeuFini = true;
        resetGrille();
      }, 500); // On ajoute un délai avant l'alerte
    } else if (grille.indexOf("") === -1) {
      setTimeout(function () {
        // Aucun joueur n'a gagné et la grille est pleine, donc c'est un match nul
        matchNuls++;
        document.getElementById('matchNuls').textContent = matchNuls;
        alert("Match nul !");
        jeuFini = true;
        resetGrille();
      }, 500); // On ajoute un délai avant l'alerte
    } else {
      // On passe au tour de l'autre joueur si le jeu n'est pas fini
      joueurActuel = joueur === 'humain' ? 'ia' : 'humain';
      if (joueurActuel === 'ia') {
        setTimeout(jouerCoupIA, 500);
      }
    }
  }
}

// Fonction pour que l'IA joue son coup en utilisant l'algorithme minimax
function jouerCoupIA() {
  // Vérifie si le jeu n'est pas terminé et si c'est le tour de l'IA
  if (!jeuFini && joueurActuel === "ia") {
    // Recherche le premier emplacement vide dans la grille
    const emplacementLibre = grille.findIndex((caseVide) => caseVide === "");
    // Si la grille n'est pas pleine
    if (emplacementLibre !== -1) {
      // On initialise les variables pour le meilleur coup et son indice
      let bestMove = -Infinity;
      let bestMoveIndex = -1;
      // Puis on parcourt chaque case de la grille
      for (let i = 0; i < grille.length; i++) {
        // On vérifie si la case est vide
        if (grille[i] === "") {
          // Si oui, on simule le coup de l'IA (marque "O" dans la case)
          grille[i] = "O";
          // Puis on utilise l'algorithme minimax pour évaluer le coup
          let moveValue = minmax(grille, 0, false);
          // On annule le coup simulé pour explorer les autres possibilités
          grille[i] = "";
          // Enfin, on met à jour le meilleur coup et son indice si le coup actuel est meilleur
          if (moveValue > bestMove) {
            bestMove = moveValue;
            bestMoveIndex = i;
          }
        }
      }
      // Joue le meilleur coup calculé par l'IA
      jouerCoup(bestMoveIndex, 'ia');
    }
  }
}


// On réinitialise la grille du jeu et l'état du jeu
function resetGrille() {
  grille.fill("");
  joueurActuel = "humain";
  jeuFini = false;
  $('.case').removeClass("humain ia");
}

// Gestionnaire d'événements pour le bouton "Commencer la partie"
$('#startGame').click(function () {
  const selectedFirstPlayer = document.getElementById('firstPlayer').value;
  resetGrille();
  joueurActuel = selectedFirstPlayer; // On définit le joueur actuel en fonction du choix de l'utilisateur

  if (selectedFirstPlayer === "ia") {
    // Si l'IA commence en premier, on joue son premier coup au milieu
    jouerCoup(4, 'ia'); // 4 correspond à l'emplacement du milieu
    joueurActuel = 'humain'; // On passe au tour du joueur humain
  } else if (selectedFirstPlayer === "humain") {
    // Si c'est au tour du joueur humain, on attend son coup
  }
});

// Gestionnaire d'événement pour les cases du morpion
$('.case').click(function () {
  jouerCoup(this.id, 'humain');
});


// Gestionnaire d'événement pour le bouton "Réinitialiser"
$('#reset').click(function () {
  // On réinitialise la grille, les scores et le nombre de matchs nuls
  resetGrille();
  playerScore = 0;
  computerScore = 0;
  matchNuls = 0;
  document.getElementById('playerScore').textContent = playerScore;
  document.getElementById('computerScore').textContent = computerScore;
  document.getElementById('matchNuls').textContent = matchNuls;
});

// On évalue l'état du morpion et attribue un score en fonction du résultat
function evaluerMorpion() {
  if (estGagnant("O")) {
    return 10; // L'IA gagne
  } else if (estGagnant("X")) {
    return -10; // Le joueur humain gagne
  } else {
    return 0; // Match nul
  }
}

// Fonction principale qui utilise l'algorithme minimax pour déterminer le meilleur coup pour l'IA
function minmax(grille, coup, maximiser) {
  // Évalue le score actuel de la position de la grille
  const score = evaluerMorpion();
  // Si l'IA gagne, retourne un score positif ajusté par le nombre de coups
  if (score === 10) {
    return score - coup;
  }
  // Si le joueur adverse gagne, retourne un score négatif ajusté par le nombre de coups
  if (score === -10) {
    return score + coup;
  }
  // Si la grille est pleine, c'est un match nul, retourne 0
  if (grille.indexOf("") === -1) {
    return 0;
  }
  // Si c'est le tour de l'IA de jouer (maximiser)
  if (maximiser) {
    let bestScore = -Infinity;
    // Parcourt chaque case de la grille
    for (let i = 0; i < 9; i++) {
      // Vérifie si la case est vide
      if (grille[i] === "") {
        // Simule le coup de l'IA (marque "O" dans la case)
        grille[i] = "O";
        // Appelle récursivement la fonction minimax pour évaluer le meilleur score
        bestScore = Math.max(bestScore, minmax(grille, coup + 1, false));
        // Annule le coup simulé pour explorer les autres possibilités
        grille[i] = "";
      }
    }
    // Retourne le meilleur score pour l'IA
    return bestScore;
  } else { // Si c'est le tour du joueur adverse de jouer (minimiser)
    let bestScore = Infinity;
    // Parcourt chaque case de la grille
    for (let i = 0; i < 9; i++) {
      // Vérifie si la case est vide
      if (grille[i] === "") {
        // Simule le coup du joueur adverse (marque "X" dans la case)
        grille[i] = "X";
        // Appelle récursivement la fonction minimax pour évaluer le meilleur score
        bestScore = Math.min(bestScore, minmax(grille, coup + 1, true));
        // Annule le coup simulé pour explorer les autres possibilités
        grille[i] = "";
      }
    }
    // Retourne le meilleur score pour le joueur adverse
    return bestScore;
  }
}

