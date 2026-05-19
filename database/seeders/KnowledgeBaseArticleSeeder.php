<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KnowledgeBaseArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les catégories et utilisateurs
        $categories = \App\Models\KnowledgeBaseCategory::all();
        $users = \App\Models\User::all();
        
        if ($categories->isEmpty() || $users->isEmpty()) {
            return;
        }
        
        $articles = [
            [
                'title' => 'Comment créer un nouveau ticket',
                'slug' => 'comment-creer-un-nouveau-ticket',
                'content' => '<h2>Étapes pour créer un nouveau ticket</h2>
                    <p>Pour créer un nouveau ticket dans le système CJES Support, suivez ces étapes simples :</p>
                    <ol>
                        <li><strong>Connectez-vous</strong> à votre compte CJES Support</li>
                        <li>Cliquez sur le bouton <strong>"Nouveau Ticket"</strong> dans le menu de navigation</li>
                        <li>Remplissez le formulaire avec les informations requises :
                            <ul>
                                <li><strong>Sujet</strong> : Titre clair et concis de votre demande</li>
                                <li><strong>Description</strong> : Détails complets du problème</li>
                                <li><strong>Catégorie</strong> : Choisissez la catégorie appropriée</li>
                                <li><strong>Priorité</strong> : Sélectionnez le niveau d\'urgence</li>
                            </ul>
                        </li>
                        <li>Cliquez sur <strong>"Soumettre"</strong> pour créer le ticket</li>
                    </ol>
                    <h3>Bonnes pratiques</h3>
                    <ul>
                        <li>Utilisez un titre descriptif pour faciliter la recherche</li>
                        <li>Fournissez autant de détails que possible</li>
                        <li>Joignez des captures d\'écran si nécessaire</li>
                        <li>Indiquez les messages d\'erreur exacts</li>
                    </ul>',
                'excerpt' => 'Guide complet pour créer un nouveau ticket dans le système CJES Support',
                'category_id' => 1, // Guide de démarrage
                'author_id' => $users->first()->id,
                'updater_id' => $users->first()->id,
                'tags' => 'ticket, création, nouveau, support, guide',
                'published' => true,
                'published_at' => now(),
                'views_count' => 45,
                'helpful_count' => 12,
                'not_helpful_count' => 1,
            ],
            [
                'title' => 'Résoudre les problèmes de connexion',
                'slug' => 'resoudre-problemes-connexion',
                'content' => '<h2>Dépannage des problèmes de connexion</h2>
                    <p>Si vous rencontrez des difficultés pour vous connecter à CJES Support, voici les solutions les plus courantes :</p>
                    
                    <h3>1. Vérifiez vos identifiants</h3>
                    <ul>
                        <li>Assurez-vous que votre email et mot de passe sont corrects</li>
                        <li>Vérifiez que la touche Maj (Caps Lock) n\'est pas activée</li>
                        <li>Essayez de copier-coller vos identifiants</li>
                    </ul>
                    
                    <h3>2. Réinitialisez votre mot de passe</h3>
                    <p>Si vous avez oublié votre mot de passe :</p>
                    <ol>
                        <li>Cliquez sur <strong>"Mot de passe oublié"</strong></li>
                        <li>Entrez votre adresse email</li>
                        <li>Suivez les instructions envoyées par email</li>
                        <li>Créez un nouveau mot de passe sécurisé</li>
                    </ol>
                    
                    <h3>3. Vérifiez votre navigateur</h3>
                    <ul>
                        <li>Effacez les cookies et le cache de votre navigateur</li>
                        <li>Essayez avec un autre navigateur (Chrome, Firefox, Edge)</li>
                        <li>Désactivez temporairement les extensions</li>
                    </ul>
                    
                    <h3>4. Problèmes réseau</h3>
                    <ul>
                        <li>Vérifiez votre connexion Internet</li>
                        <li>Essayez de vous connecter depuis un autre réseau</li>
                        <li>Contactez votre service informatique si le problème persiste</li>
                    </ul>',
                'excerpt' => 'Solutions aux problèmes de connexion les plus fréquents',
                'category_id' => 2, // Résolution de problèmes
                'author_id' => $users->first()->id,
                'updater_id' => $users->first()->id,
                'tags' => 'connexion, login, mot de passe, dépannage, erreur',
                'published' => true,
                'published_at' => now()->subDays(2),
                'views_count' => 78,
                'helpful_count' => 23,
                'not_helpful_count' => 3,
            ],
            [
                'title' => 'Procédure d\'assignation des tickets',
                'slug' => 'procedure-assignation-tickets',
                'content' => '<h2>Processus d\'assignation des tickets</h2>
                    <p>Cet article décrit la procédure standard pour assigner les tickets aux techniciens dans CJES Support.</p>
                    
                    <h3>1. Réception du ticket</h3>
                    <ul>
                        <li>Les nouveaux tickets apparaissent dans la file d\'attente</li>
                        <li>Une notification est envoyée aux superviseurs</li>
                        <li>Le ticket est automatiquement classé par priorité</li>
                    </ul>
                    
                    <h3>2. Évaluation du ticket</h3>
                    <p>Avant l\'assignation, évaluez :</p>
                    <ul>
                        <li><strong>Catégorie</strong> : Type de problème</li>
                        <li><strong>Priorité</strong> : Urgence et impact</li>
                        <li><strong>Compétences requises</strong> : Expertise nécessaire</li>
                        <li><strong>Charge de travail</strong> : Disponibilité des techniciens</li>
                    </ul>
                    
                    <h3>3. Sélection du technicien</h3>
                    <p>Choisissez le technicien approprié en fonction de :</p>
                    <ul>
                        <li>Spécialisation et compétences</li>
                        <li>Charge de travail actuelle</li>
                        <li>Disponibilité</li>
                        <li>Historique des tickets similaires</li>
                    </ul>
                    
                    <h3>4. Assignation</h3>
                    <ol>
                        <li>Ouvrez le ticket concerné</li>
                        <li>Cliquez sur <strong>"Assigner"</strong></li>
                        <li>Sélectionnez le technicien dans la liste</li>
                        <li>Ajoutez des commentaires si nécessaire</li>
                        <li>Confirmez l\'assignation</li>
                    </ol>
                    
                    <h3>5. Suivi post-assignation</h3>
                    <ul>
                        <li>Le technicien reçoit une notification</li>
                        <li>Le superviseur peut suivre l\'avancement</li>
                        <li>Des rappels automatiques sont envoyés</li>
                    </ul>',
                'excerpt' => 'Guide complet pour l\'assignation des tickets aux techniciens',
                'category_id' => 3, // Procédures
                'author_id' => $users->first()->id,
                'updater_id' => $users->first()->id,
                'tags' => 'assignation, ticket, technicien, procédure, workflow',
                'published' => true,
                'published_at' => now()->subDays(5),
                'views_count' => 92,
                'helpful_count' => 31,
                'not_helpful_count' => 2,
            ],
            [
                'title' => 'Configuration des notifications par email',
                'slug' => 'configuration-notifications-email',
                'content' => '<h2>Personnaliser vos notifications email</h2>
                    <p>Apprenez à configurer vos préférences de notification pour rester informé des mises à jour importantes.</p>
                    
                    <h3>Accès aux paramètres</h3>
                    <ol>
                        <li>Connectez-vous à CJES Support</li>
                        <li>Cliquez sur votre profil en haut à droite</li>
                        <li>Sélectionnez <strong>"Paramètres"</strong></li>
                        <li>Allez dans l\'onglet <strong>"Notifications"</strong></li>
                    </ol>
                    
                    <h3>Types de notifications disponibles</h3>
                    <ul>
                        <li><strong>Nouveaux tickets</strong> : Alertes pour les nouvelles demandes</li>
                        <li><strong>Assignations</strong> : Tickets qui vous sont assignés</li>
                        <li><strong>Mises à jour</strong> : Changements de statut</li>
                        <li><strong>Commentaires</strong> : Nouvelles réponses</li>
                        <li><strong>Rappels</strong> : Tickets en attente</li>
                    </ul>
                    
                    <h3>Configuration par rôle</h3>
                    
                    <h4>Pour les techniciens</h4>
                    <ul>
                        <li>Activez les notifications d\'assignation</li>
                        <li>Configurez les rappels pour tickets en attente</li>
                        <li>Désactivez les notifications de nouveaux tickets</li>
                    </ul>
                    
                    <h4>Pour les superviseurs</h4>
                    <ul>
                        <li>Activez tous les types de notifications</li>
                        <li>Configurez les alertes de haute priorité</li>
                        <li>Activez les rapports quotidiens</li>
                    </ul>
                    
                    <h3>Fréquence des notifications</h3>
                    <ul>
                        <li><strong>Immédiat</strong> : Notification en temps réel</li>
                        <li><strong>Horaire</strong> : Regroupement par heure</li>
                        <li><strong>Quotidien</strong> : Résumé quotidien</li>
                        <li><strong>Hebdomadaire</strong> : Rapport hebdomadaire</li>
                    </ul>
                    
                    <h3>Bonnes pratiques</h3>
                    <ul>
                        <li>Évitez la surcharge de notifications</li>
                        <li>Utilisez les filtres intelligents</li>
                        <li>Configurez des heures de réception</li>
                        <li>Vérifiez vos spam régulièrement</li>
                    </ul>',
                'excerpt' => 'Guide pour personnaliser les notifications email selon vos besoins',
                'category_id' => 4, // Configuration
                'author_id' => $users->first()->id,
                'updater_id' => $users->first()->id,
                'tags' => 'notification, email, configuration, paramètres, alertes',
                'published' => true,
                'published_at' => now()->subDays(3),
                'views_count' => 56,
                'helpful_count' => 18,
                'not_helpful_count' => 1,
            ],
            [
                'title' => 'Politique de sécurité des mots de passe',
                'slug' => 'politique-securite-mots-de-passe',
                'content' => '<h2>Règles de sécurité des mots de passe</h2>
                    <p>Cette politique définit les exigences de sécurité pour les mots de passe dans CJES Support.</p>
                    
                    <h3>Exigences minimales</h3>
                    <ul>
                        <li><strong>Longueur minimale</strong> : 8 caractères</li>
                        <li><strong>Complexité</strong> : Doit contenir au moins :
                            <ul>
                                <li>Une lettre majuscule (A-Z)</li>
                                <li>Une lettre minuscule (a-z)</li>
                                <li>Un chiffre (0-9)</li>
                                <li>Un caractère spécial (!@#$%^&*)</li>
                            </ul>
                        </li>
                        <li><strong>Ancienneté</strong> : Changement tous les 90 jours</li>
                        <li><strong>Historique</strong> : Pas de réutilisation des 5 derniers mots de passe</li>
                    </ul>
                    
                    <h3>Bonnes pratiques</h3>
                    <ul>
                        <li>Utilisez des phrases de passe plutôt que des mots simples</li>
                        <li>Évitez les informations personnelles (nom, date de naissance)</li>
                        <li>N\'utilisez pas le même mot de passe sur plusieurs systèmes</li>
                        <li>Ne partagez jamais vos identifiants</li>
                        <li>Changez immédiatement votre mot de passe en cas de suspicion de compromission</li>
                    </ul>
                    
                    <h3>Procédure de changement</h3>
                    <ol>
                        <li>Connectez-vous à votre compte</li>
                        <li>Accédez à <strong>"Mon profil"</strong></li>
                        <li>Cliquez sur <strong>"Changer le mot de passe"</strong></li>
                        <li>Entrez votre mot de passe actuel</li>
                        <li>Définissez le nouveau mot de passe</li>
                        <li>Confirmez le nouveau mot de passe</li>
                        <li>Cliquez sur <strong>"Mettre à jour"</strong></li>
                    </ol>
                    
                    <h3>Verrouillage du compte</h3>
                    <p>Pour des raisons de sécurité :</p>
                    <ul>
                        <li>3 tentatives de connexion échouées = verrouillage temporaire (15 minutes)</li>
                        <li>5 tentatives échouées = verrouillage administrateur</li>
                        <li>Le déverrouillage nécessite une intervention du support technique</li>
                    </ul>
                    
                    <h3>Stockage sécurisé</h3>
                    <ul>
                        <li>Utilisez un gestionnaire de mots de passe approuvé</li>
                        <li>Activez l\'authentification à deux facteurs</li>
                        <li>Évitez de noter vos mots de passe</li>
                        <li>Déconnectez-vous après chaque session</li>
                    </ul>',
                'excerpt' => 'Règles et bonnes pratiques pour la sécurité des mots de passe',
                'category_id' => 5, // Sécurité
                'author_id' => $users->first()->id,
                'updater_id' => $users->first()->id,
                'tags' => 'sécurité, mot de passe, politique, authentification, protection',
                'published' => true,
                'published_at' => now()->subDays(7),
                'views_count' => 103,
                'helpful_count' => 42,
                'not_helpful_count' => 0,
            ],
            [
                'title' => 'Intégration avec Active Directory',
                'slug' => 'integration-active-directory',
                'content' => '<h2>Configuration de l\'intégration Active Directory</h2>
                    <p>Ce guide explique comment intégrer CJES Support avec votre infrastructure Active Directory existante.</p>
                    
                    <h3>Prérequis</h3>
                    <ul>
                        <li>Active Directory Server 2016 ou plus récent</li>
                        <li>Accès administrateur au domaine</li>
                        <li>Certificat SSL pour le LDAP</li>
                        <li>Port 389 (LDAP) ou 636 (LDAPS) ouvert</li>
                    </ul>
                    
                    <h3>Étape 1: Configuration du serveur LDAP</h3>
                    <ol>
                        <li>Ouvrez la console <strong>"Services AD DS"</strong></li>
                        <li>Vérifiez que le service LDAP est en cours d\'exécution</li>
                        <li>Configurez les paramètres de sécurité LDAP</li>
                        <li>Activez le chiffrement si nécessaire</li>
                    </ol>
                    
                    <h3>Étape 2: Configuration dans CJES Support</h3>
                    <ol>
                        <li>Connectez-vous en tant qu\'administrateur</li>
                        <li>Allez dans <strong>"Administration" > "Intégrations"</strong></li>
                        <li>Sélectionnez <strong>"Active Directory"</strong></li>
                        <li>Remplissez les champs requis :
                            <ul>
                                <li><strong>Serveur LDAP</strong> : ad.votre-entreprise.com</li>
                                <li><strong>Port</strong> : 389 (LDAP) ou 636 (LDAPS)</li>
                                <li><strong>Distinguished Name</strong> : DC=votre-entreprise,DC=com</li>
                                <li><strong>Utilisateur de liaison</strong> : cn=admin,ou=users,dc=votre-entreprise,dc=com</li>
                                <li><strong>Filtre utilisateur</strong> : (objectClass=user)</li>
                            </ul>
                        </li>
                        <li>Testez la connexion</li>
                        <li>Sauvegardez la configuration</li>
                    </ol>
                    
                    <h3>Étape 3: Synchronisation des utilisateurs</h3>
                    <ol>
                        <li>Configurez le mappage des attributs :
                            <ul>
                                <li>username -> sAMAccountName</li>
                                <li>email -> mail</li>
                                <li>nom -> sn</li>
                                <li>prénom -> givenName</li>
                            </ul>
                        </li>
                        <li>Définissez les filtres de synchronisation</li>
                        <li>Planifiez la synchronisation automatique</li>
                        <li>Lancez la première synchronisation</li>
                    </ol>
                    
                    <h3>Étape 4: Test et validation</h3>
                    <ul>
                        <li>Vérifiez que les utilisateurs sont synchronisés</li>
                        <li>Testez la connexion avec un compte AD</li>
                        <li>Vérifiez les groupes et permissions</li>
                        <li>Validez le processus de déconnexion</li>
                    </ul>
                    
                    <h3>Dépannage courant</h3>
                    <ul>
                        <li><strong>Erreur de connexion</strong> : Vérifiez les identifiants de liaison</li>
                        <li><strong>Timeout</strong> : Vérifiez la connectivité réseau</li>
                        <li><strong>Synchronisation échouée</strong> : Vérifiez les permissions AD</li>
                        <li><strong>Certificat SSL</strong> : Vérifiez la validité du certificat</li>
                    </ul>',
                'excerpt' => 'Guide complet pour intégrer CJES Support avec Active Directory',
                'category_id' => 6, // Intégrations
                'author_id' => $users->first()->id,
                'updater_id' => $users->first()->id,
                'tags' => 'active directory, ldap, intégration, authentification, synchronisation',
                'published' => true,
                'published_at' => now()->subDays(1),
                'views_count' => 34,
                'helpful_count' => 8,
                'not_helpful_count' => 2,
            ],
            [
                'title' => 'Guide rapide pour les nouveaux techniciens',
                'slug' => 'guide-rapide-nouveaux-techniciens',
                'content' => '<h2>Bienvenue dans CJES Support !</h2>
                    <p>Ce guide vous aidera à démarrer rapidement et à comprendre les fonctionnalités essentielles du système.</p>
                    
                    <h3>Vos premières étapes</h3>
                    <ol>
                        <li><strong>Connectez-vous</strong> avec vos identifiants fournis</li>
                        <li><strong>Complétez votre profil</strong> avec vos informations</li>
                        <li><strong>Familiarisez-vous</strong> avec l\'interface</li>
                        <li><strong>Consultez</strong> les tickets assignés</li>
                    </ol>
                    
                    <h3>Interface principale</h3>
                    <ul>
                        <li><strong>Dashboard</strong> : Vue d\'ensemble de vos activités</li>
                        <li><strong>Mes Tickets</strong> : Tickets qui vous sont assignés</li>
                        <li><strong>Tickets</strong> : Tous les tickets du système</li>
                        <li><strong>Base de connaissances</strong> : Documentation et guides</li>
                    </ul>
                    
                    <h3>Gestion des tickets</h3>
                    
                    <h4>Accepter un ticket</h4>
                    <ol>
                        <li>Allez dans <strong>"Assignations"</strong></li>
                        <li>Cliquez sur un ticket non assigné</li>
                        <li>Revoyez les détails du problème</li>
                        <li>Cliquez sur <strong>"Accepter"</strong></li>
                    </ol>
                    
                    <h4>Traiter un ticket</h4>
                    <ol>
                        <li>Analysez le problème décrit</li>
                        <li>Recherchez des solutions similaires</li>
                        <li>Contactez le client si nécessaire</li>
                        <li>Documentez vos actions</li>
                        <li>Mettez à jour le statut</li>
                    </ol>
                    
                    <h3>Statuts des tickets</h3>
                    <ul>
                        <li><strong>Nouveau</strong> : Ticket créé, en attente d\'assignation</li>
                        <li><strong>Assigné</strong> : Ticket assigné à un technicien</li>
                        <li><strong>En cours</strong> : Technicien travaille sur le ticket</li>
                        <li><strong>En attente</strong> : En attente de réponse du client</li>
                        <li><strong>Résolu</strong> : Problème solutionné</li>
                        <li><strong>Fermé</strong> : Ticket terminé</li>
                    </ul>
                    
                    <h3>Bonnes pratiques</h3>
                    <ul>
                        <li>Répondez rapidement aux nouveaux tickets</li>
                        <li>Documentez toutes vos actions</li>
                        <li>Soyez professionnel dans vos communications</li>
                        <li>Utilisez la base de connaissances</li>
                        <li>Demandez de l\'aide si nécessaire</li>
                    </ul>
                    
                    <h3>Outils utiles</h3>
                    <ul>
                        <li><strong>Recherche</strong> : Trouvez rapidement des informations</li>
                        <li><strong>Filtres</strong> : Organisez vos tickets</li>
                        <li><strong>Notifications</strong> : Restez informé</li>
                        <li><strong>Rapports</strong> : Suivez vos performances</li>
                    </ul>
                    
                    <h3>Support et aide</h3>
                    <ul>
                        <li>Contactez votre superviseur pour toute question</li>
                        <li>Consultez la base de connaissances</li>
                        <li>Participez aux formations disponibles</li>
                        <li>Rejoignez les canaux de communication internes</li>
                    </ul>',
                'excerpt' => 'Guide complet pour les nouveaux techniciens rejoignant CJES Support',
                'category_id' => 1, // Guide de démarrage
                'author_id' => $users->first()->id,
                'updater_id' => $users->first()->id,
                'tags' => 'nouveau technicien, démarrage, guide, formation, bienvenue',
                'published' => true,
                'published_at' => now()->subDays(4),
                'views_count' => 67,
                'helpful_count' => 25,
                'not_helpful_count' => 1,
            ],
            [
                'title' => 'Optimisation des performances du système',
                'slug' => 'optimisation-performances-systeme',
                'content' => '<h2>Guide d\'optimisation des performances</h2>
                    <p>Découvrez comment améliorer les performances de CJES Support pour une meilleure expérience utilisateur.</p>
                    
                    <h3>1. Optimisation du navigateur</h3>
                    <ul>
                        <li>Mettez à jour votre navigateur vers la dernière version</li>
                        <li>Effacez régulièrement le cache et les cookies</li>
                        <li>Désactivez les extensions non nécessaires</li>
                        <li>Limitez le nombre d\'onglets ouverts</li>
                    </ul>
                    
                    <h3>2. Configuration réseau</h3>
                    <ul>
                        <li>Utilisez une connexion Internet stable</li>
                        <li>Privilégiez le connexion Ethernet au WiFi</li>
                        <li>Vérifiez la qualité de votre connexion</li>
                        <li>Évitez les périodes de pointe réseau</li>
                    </ul>
                    
                    <h3>3. Paramètres système</h3>
                    <ul>
                        <li>Fermez les applications inutiles</li>
                        <li>Vérifiez l\'utilisation de la RAM</li>
                        <li>Assurez-vous d\'avoir suffisamment d\'espace disque</li>
                        <li>Redémarrez régulièrement votre ordinateur</li>
                    </ul>
                    
                    <h3>4. Utilisation efficace de CJES Support</h3>
                    <ul>
                        <li>Utilisez les filtres pour réduire les chargements</li>
                        <li>Évitez les rafraîchissements manuels</li>
                        <li>Utilisez la recherche plutôt que la navigation</li>
                        <li>Configurez les notifications intelligentes</li>
                    </ul>
                    
                    <h3>5. Surveillance des performances</h3>
                    <ul>
                        <li>Surveillez le temps de chargement des pages</li>
                        <li>Vérifiez l\'utilisation CPU pendant l\'utilisation</li>
                        <li>Notez les ralentissements récurrents</li>
                        <li>Documentez les problèmes rencontrés</li>
                    </ul>
                    
                    <h3>6. Dépannage courant</h3>
                    <ul>
                        <li><strong>Pages lentes</strong> : Vérifiez votre connexion</li>
                        <li><strong>Interface figée</strong> : Redémarrez le navigateur</li>
                        <li><strong>Erreurs fréquentes</strong> : Videz le cache</li>
                        <li><strong>Déconnexions</strong> : Vérifiez la stabilité réseau</li>
                    </ul>
                    
                    <h3>7. Recommandations matérielles</h3>
                    <ul>
                        <li><strong>RAM</strong> : Minimum 8GB, recommandé 16GB</li>
                        <li><strong>Processeur</strong> : Intel i5 ou AMD Ryzen 5 minimum</li>
                        <li><strong>Stockage</strong> : SSD recommandé</li>
                        <li><strong>Écran</strong> : Full HD (1920x1080) minimum</li>
                    </ul>',
                'excerpt' => 'Conseils pratiques pour optimiser les performances de CJES Support',
                'category_id' => 2, // Résolution de problèmes
                'author_id' => $users->first()->id,
                'updater_id' => $users->first()->id,
                'tags' => 'performance, optimisation, vitesse, dépannage, système',
                'published' => true,
                'published_at' => now()->subDays(6),
                'views_count' => 41,
                'helpful_count' => 14,
                'not_helpful_count' => 0,
            ],
        ];
        
        foreach ($articles as $articleData) {
            \App\Models\KnowledgeBase::create($articleData);
        }
    }
}
