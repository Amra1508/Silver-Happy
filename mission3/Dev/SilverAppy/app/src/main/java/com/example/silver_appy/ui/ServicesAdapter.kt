package com.example.silver_appy.ui

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.silver_appy.R
import com.example.silver_appy.network.UserReservation
import java.text.SimpleDateFormat
import java.util.Locale

class ServicesAdapter(private var services: List<UserReservation>) :
    RecyclerView.Adapter<ServicesAdapter.ViewHolder>() {

    class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val tvNom: TextView = view.findViewById(R.id.tv_item_title)
        val tvDescription: TextView = view.findViewById(R.id.tv_item_location)
        val tvDate: TextView = view.findViewById(R.id.tv_item_date)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_activity, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val reservation = services[position]

        holder.tvNom.text = reservation.nom
        holder.tvDescription.text = reservation.description ?: ""

        try {
            val parser = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ssXXX", Locale.FRANCE)
            val formatter = SimpleDateFormat("dd/MM/yyyy 'à' HH'h'mm", Locale.FRANCE)
            val parsedDate = parser.parse(reservation.dateHeure)

            if (parsedDate != null) {
                holder.tvDate.text = formatter.format(parsedDate)
            } else {
                holder.tvDate.text = reservation.dateHeure
            }
        } catch (e: Exception) {
            holder.tvDate.text = reservation.dateHeure.substringBefore("T")
        }
    }

    override fun getItemCount(): Int = services.size

    fun updateData(newServices: List<UserReservation>) {
        this.services = newServices
        notifyDataSetChanged()
    }
}