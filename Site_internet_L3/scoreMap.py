import csv
import os
import json
from collections import Counter

# Etape 1 - Traiter la base de données

# Dossier contenant les fichiers texte
text_files_folder = 'sampled_train/sampled_train/'

# Listes pour stocker les textes et les labels
texts = []
labels = []
csv_file_path = 'sampled_train/annotations_metadata.csv'
with open(csv_file_path, 'r', encoding='utf-8') as csv_file:
    csv_reader = csv.reader(csv_file)
    next(csv_reader)  # Ignorer l'en-tête du CSV s'il y en a un
    for row in csv_reader:
        # row[0] contient le label, row[1] le nom du fichier texte
        label = row[4]
        text_file_name = row[0]
        
        # Construire le chemin complet du fichier texte
        text_file_path = text_files_folder+text_file_name+'.txt'
        text_content = ""
    
        if os.path.exists(text_file_path):
            # Lire le contenu du fichier texte
            with open(text_file_path, 'r', encoding='utf-8') as text_file:
                text_content = text_file.read()
        
        # Ajouter le texte et le label aux listes
        texts.append(text_content)
        labels.append(label)

# Etape 2 - Calcul du IDF

# Compter le nombre de documents dans lesquels chaque mot apparaît au moins une fois
word_document_count = Counter()
for text in texts:
    words_in_text = set(text.split())
    word_document_count.update(words_in_text)

# Calculer l'IDF pour chaque mot
total_documents = len(texts)
idf = {word: total_documents / (count + 1) for word, count in word_document_count.items()}

# Etape 3 - Association TF-IDF

score_map = {word: 0 for word in idf.keys()}

for i, text in enumerate(texts):
    words = text.split()
    label = labels[i]
    for word in words:
        tf = words.count(word) / len(words)
        tf_idf = tf * idf[word]

        if label == 'hate':
            score_map[word] -= tf_idf
        else:
            score_map[word] += tf_idf

# Stocker les valeurs de ScoreMap dans un fichier JSON
score_map_file = 'C:\wamp64\www\Senecaille\score_map.json'
with open(score_map_file, 'w') as json_file:
    json.dump(score_map, json_file)
