package com.example.silver_appy.network

import com.google.gson.annotations.SerializedName

data class LoginCredentials(
    val email: String,
    val mdp: String
)

data class LoginResponse(
    val message: String,
    val statut: String
)

data class Utilisateur(
    val id: Long,
    val prenom: String,
    val nom: String,
    @SerializedName("date_naissance") val dateNaissance: String?,
    @SerializedName("num_telephone") val numTelephone: String?,
    val email: String,
    val pays: String?,
    val adresse: String?,
    val ville: String?,
    @SerializedName("code_postal") val codePostal: String?,
    val statut: String?,
    @SerializedName("date_creation") val dateCreation: String?,
    @SerializedName("premiere_connexion") val premiereConnexion: Long?,
    @SerializedName("id_abonnement") val idAbonnement: Long?,
    @SerializedName("debut_abonnement") val debutAbonnement: String?,
    @SerializedName("motif_bannissement") val motifBannissement: String?,
    @SerializedName("duree_bannissement") val dureeBannissement: Int?,
    @SerializedName("type_paiement") val typePaiement: String?,
    @SerializedName("est_lu") val estLu: Int?
)

data class RegisterRequest(
    val prenom: String,
    val nom: String,
    val email: String,
    val mdp: String,
    @SerializedName("date_naissance") val dateNaissance: String,
    @SerializedName("num_telephone") val numTelephone: String,
    val adresse: String,
    val ville: String,
    @SerializedName("code_postal") val codePostal: String,
    val pays: String
)

data class UpdateProfileRequest(
    val prenom: String,
    val nom: String,
    val email: String,
    @SerializedName("date_naissance") val dateNaissance: String,
    @SerializedName("num_telephone") val numTelephone: String,
    val adresse: String,
    val ville: String,
    @SerializedName("code_postal") val codePostal: String,
    val pays: String
)

data class Evenement(
    @SerializedName("id_evenement") val id: Int,
    val nom: String,
    val description: String?,
    val lieu: String?,
    @SerializedName("nombre_place") val nombrePlace: Int,
    val image: String?,
    @SerializedName("date_debut") val dateDebut: String,
    @SerializedName("date_fin") val dateFin: String?,
    @SerializedName("id_categorie") val idCategorie: Int?,
    val prix: Double
)

data class UserReservation(
    @SerializedName("id_reservation") val idReservation: Int,
    @SerializedName("id_service") val idService: Int,
    val nom: String,
    val description: String?,
    @SerializedName("date_heure") val dateHeure: String
)

data class ConseilResponse(
    val currentPage: Int,
    val data: List<ConseilModel>,
    val total: Int,
    val totalPages: Int
)

data class ConseilModel(
    val id: Long,
    val titre: String,
    val description: String,
    val date: String,
    val categorie: String,
    val likes: Int,
    @SerializedName("is_liked") val isLiked: Boolean
)