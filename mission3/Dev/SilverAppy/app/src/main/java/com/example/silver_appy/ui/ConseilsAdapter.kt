package com.example.silver_appy.ui

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.silver_appy.R
import com.example.silver_appy.network.ConseilModel
import java.text.SimpleDateFormat
import java.util.Locale

class ConseilsAdapter(private var conseils: List<ConseilModel>) :
    RecyclerView.Adapter<ConseilsAdapter.ViewHolder>() {

    class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val tvTitre: TextView = view.findViewById(R.id.tv_item_title)
        val tvDescription: TextView = view.findViewById(R.id.tv_item_location)
        val tvPrix: TextView = view.findViewById(R.id.tv_item_price)
        val tvDate: TextView = view.findViewById(R.id.tv_item_date)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_activity, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val conseil = conseils[position]

        holder.tvTitre.text = conseil.titre
        holder.tvDescription.text = "${conseil.categorie} • ${conseil.description}"
        holder.tvPrix.text = ""

        try {
            val parser = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ssXXX", Locale.FRANCE)
            val formatter = SimpleDateFormat("dd/MM/yyyy", Locale.FRANCE)
            val parsedDate = parser.parse(conseil.date)

            if (parsedDate != null) {
                holder.tvDate.text = formatter.format(parsedDate)
            } else {
                holder.tvDate.text = conseil.date
            }
        } catch (e: Exception) {
            holder.tvDate.text = conseil.date.substringBefore("T")
        }
    }

    override fun getItemCount(): Int = conseils.size

    fun updateData(newConseils: List<ConseilModel>) {
        this.conseils = newConseils
        notifyDataSetChanged()
    }
}