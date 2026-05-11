package com.example.silver_appy.ui

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.silver_appy.network.LoginCredentials
import com.example.silver_appy.network.RegisterRequest
import com.example.silver_appy.network.RetrofitClient
import kotlinx.coroutines.launch

class AuthViewModel : ViewModel() {

    fun register(request: RegisterRequest, onSuccess: () -> Unit, onError: (String) -> Unit) {
        viewModelScope.launch {
            try {
                val response = RetrofitClient.apiService.register(request)
                if (response.isSuccessful) {
                    onSuccess()
                } else {
                    val vraiMessageServeur = response.errorBody()?.string() ?: "Erreur inconnue"
                    onError(vraiMessageServeur)
                }
            } catch (e: Exception) {
                onError("Problème de connexion au serveur : ${e.message}")
            }
        }
    }

    fun login(email: String, motDePasse: String, onSuccess: () -> Unit, onError: (String) -> Unit) {
        viewModelScope.launch {
            try {
                if (email.isNotBlank() && motDePasse.isNotBlank()) {
                    val creds = LoginCredentials(email, motDePasse)
                    val response = RetrofitClient.apiService.login(creds)

                    if (response.isSuccessful) {
                        onSuccess()
                    } else {
                        onError("Identifiants incorrects")
                    }
                } else {
                    onError("Veuillez remplir tous les champs")
                }
            } catch (e: Exception) {
                onError("Erreur réseau")
            }
        }
    }

    fun logout(onComplete: () -> Unit) {
        viewModelScope.launch {
            try {
                RetrofitClient.apiService.logout()
            } catch (e: Exception) {
            } finally {
                onComplete()
            }
        }
    }
}