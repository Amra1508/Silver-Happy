package com.example.silver_appy.ui

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.silver_appy.network.ConseilModel
import com.example.silver_appy.network.Evenement
import com.example.silver_appy.network.RetrofitClient
import com.example.silver_appy.network.UpdateProfileRequest
import com.example.silver_appy.network.UserReservation
import com.example.silver_appy.network.Utilisateur
import kotlinx.coroutines.launch

class MainViewModel : ViewModel() {

    private val _activities = MutableLiveData<List<Evenement>>()
    val activities: LiveData<List<Evenement>> = _activities

    private val _user = MutableLiveData<Utilisateur?>()
    val user: LiveData<Utilisateur?> = _user

    fun fetchData() {
        viewModelScope.launch {
            try {
                val meResponse = RetrofitClient.apiService.getMe()
                if (meResponse.isSuccessful && meResponse.body() != null) {
                    val utilisateur = meResponse.body()!!
                    _user.postValue(utilisateur)

                    fetchActivities(utilisateur.id)
                    fetchServices(utilisateur.id)
                    fetchConseils()
                }
            } catch (e: Exception) {
                _user.postValue(null)
            }
        }
    }

    private val _conseils = MutableLiveData<List<ConseilModel>>()
    val conseils: LiveData<List<ConseilModel>> = _conseils

    fun fetchConseils() {
        viewModelScope.launch {
            try {
                val response = RetrofitClient.apiService.getConseils()
                if (response.isSuccessful && response.body() != null) {
                    _conseils.postValue(response.body()!!.data)
                } else {
                    _conseils.postValue(emptyList())
                }
            } catch (e: Exception) {
                _conseils.postValue(emptyList())
            }
        }
    }

    private val _services = MutableLiveData<List<UserReservation>>()
    val services: LiveData<List<UserReservation>> = _services

    fun fetchServices(userId: Long) {
        viewModelScope.launch {
            try {
                val response = RetrofitClient.apiService.getUserServices(userId)
                if (response.isSuccessful && response.body() != null) {
                    _services.postValue(response.body()!!)
                } else {
                    _services.postValue(emptyList())
                }
            } catch (e: Exception) {
                _services.postValue(emptyList())
            }
        }
    }

    fun updateProfile(
        fullName: String,
        email: String,
        dob: String,
        phone: String,
        address: String,
        zip: String,
        city: String,
        country: String,
        onSuccess: () -> Unit,
        onError: (String) -> Unit
    ) {
        val parts = fullName.trim().split(" ", limit = 2)
        val prenom = parts.getOrNull(0) ?: ""
        val nom = parts.getOrNull(1) ?: ""

        viewModelScope.launch {
            try {
                val request = UpdateProfileRequest(
                    prenom = prenom,
                    nom = nom,
                    email = email,
                    dateNaissance = dob,
                    numTelephone = phone,
                    adresse = address,
                    codePostal = zip,
                    ville = city,
                    pays = country
                )
                val response = RetrofitClient.apiService.updateProfile(request)
                if (response.isSuccessful) {
                    fetchData()
                    onSuccess()
                } else {
                    val errorMsg = response.errorBody()?.string() ?: "Erreur"
                    onError(errorMsg)
                }
            } catch (e: Exception) {
                onError("Erreur réseau")
            }
        }
    }

    private suspend fun fetchActivities(userId: Long) {
        try {
            val eventsResponse = RetrofitClient.apiService.getUserEvenements(userId)
            if (eventsResponse.isSuccessful && eventsResponse.body() != null) {
                _activities.postValue(eventsResponse.body()!!)
            } else {
                _activities.postValue(emptyList())
            }
        } catch (e: Exception) {
            _activities.postValue(emptyList())
        }
    }
}