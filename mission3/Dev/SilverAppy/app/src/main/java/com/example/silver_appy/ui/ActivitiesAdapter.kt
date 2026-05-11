package com.example.silver_appy.ui

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.silver_appy.R
import com.example.silver_appy.network.Evenement
import java.text.SimpleDateFormat
import java.util.Locale

class ActivitiesAdapter(private var activities: List<Evenement>) :
    RecyclerView.Adapter<ActivitiesAdapter.ViewHolder>() {

    class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val tvTitle: TextView = view.findViewById(R.id.tv_item_title)
        val tvDate: TextView = view.findViewById(R.id.tv_item_date)
        val tvLocation: TextView = view.findViewById(R.id.tv_item_location)
        val tvPrice: TextView = view.findViewById(R.id.tv_item_price)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_activity, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val event = activities[position]

        holder.tvTitle.text = event.nom
        holder.tvLocation.text = event.lieu ?: "Lieu à préciser"

        if (event.prix == 0.0) {
            holder.tvPrice.text = "Gratuit"
        } else {
            holder.tvPrice.text = String.format(Locale.FRANCE, "%.2f €", event.prix)
        }

        try {
            val parser = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ssXXX", Locale.FRANCE)
            val formatter = SimpleDateFormat("dd/MM/yyyy 'à' HH'h'mm", Locale.FRANCE)
            val parsedDate = parser.parse(event.dateDebut)

            if (parsedDate != null) {
                holder.tvDate.text = formatter.format(parsedDate)
            } else {
                holder.tvDate.text = event.dateDebut
            }
        } catch (e: Exception) {
            holder.tvDate.text = event.dateDebut.substringBefore("T")
        }
    }

    override fun getItemCount(): Int = activities.size

    fun updateData(newActivities: List<Evenement>) {
        this.activities = newActivities
        notifyDataSetChanged()
    }
}