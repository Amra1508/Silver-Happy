package com.example.silver_appy.network

import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Path

interface ApiService {
    @POST("auth/login")
    suspend fun login(@Body creds: LoginCredentials): Response<LoginResponse>

    @POST("auth/logout")
    suspend fun logout(): Response<Void>

    @GET("auth/me")
    suspend fun getMe(): Response<Utilisateur>

    @GET("evenement/user/{id}")
    suspend fun getUserEvenements(@Path("id") userId: Long): Response<List<Evenement>>

    @POST("auth/register")
    suspend fun register(@Body request: RegisterRequest): Response<Utilisateur>

    @PUT("auth/update")
    suspend fun updateProfile(@Body request: UpdateProfileRequest): Response<Void>

    @GET("service/user/{id}")
    suspend fun getUserServices(@Path("id") userId: Long): Response<List<UserReservation>>

    @GET("conseil/read")
    suspend fun getConseils(): Response<ConseilResponse>
}