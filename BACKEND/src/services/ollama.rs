use crate::models::chat::ChatMessage;
use anyhow::{Context, Result};
use reqwest::Client;
use serde::{Deserialize, Serialize};
use tracing::{error, info};

#[derive(Clone)]
pub struct OllamaService {
    client: Client,
    base_url: String,
    model: String,
}

#[derive(Serialize)]
struct OllamaChatPayload {
    model: String,
    messages: Vec<OllamaMessagePayload>,
    stream: bool,
}

#[derive(Serialize, Deserialize)]
struct OllamaMessagePayload {
    role: String,
    content: String,
}

#[derive(Deserialize)]
struct OllamaChatResponsePayload {
    message: OllamaMessagePayload,
}

impl OllamaService {
    pub fn new(base_url: String, model: String) -> Self {
        Self {
            client: Client::new(),
            base_url,
            model,
        }
    }

    pub async fn generate_chat_response(
        &self,
        prompt: &str,
        history: Option<Vec<ChatMessage>>,
        field_context: Option<String>,
    ) -> Result<String> {
        let endpoint = format!("{}/api/chat", self.base_url);

        // System prompt untuk KickSwift AI Assistant
        let system_prompt = format!(
            "Anda adalah AI Assistant cerdas, ramah, dan solutif untuk platform booking lapangan futsal KickSwift. \
            Tugas Anda adalah membantu pengguna memilih jenis lapangan (Vinyl, Synthetic Grass, Interlocking), \
            menjelaskan ketersediaan slot jadwal, rincian harga sewa, dan tata cara pemesanan.\n\
            Konteks Lapangan Saat Ini:\n{}\n\
            Jawab dengan bahasa Indonesia yang sopan, ringkas, dan jelas.",
            field_context.unwrap_or_else(|| "Tersedia Lapangan Vinyl, Rumput Sintetis, dan Interlocking dengan tarif mulai Rp 130.000 - Rp 175.000/jam.".to_string())
        );

        let mut messages = vec![OllamaMessagePayload {
            role: "system".to_string(),
            content: system_prompt,
        }];

        if let Some(hist) = history {
            for msg in hist {
                messages.push(OllamaMessagePayload {
                    role: msg.role,
                    content: msg.content,
                });
            }
        }

        messages.push(OllamaMessagePayload {
            role: "user".to_string(),
            content: prompt.to_string(),
        });

        let payload = OllamaChatPayload {
            model: self.model.clone(),
            messages,
            stream: false,
        };

        info!("Sending prompt to Ollama at {} using model {}", endpoint, self.model);

        let response = self
            .client
            .post(&endpoint)
            .json(&payload)
            .send()
            .await
            .context("Gagal menghubungi server Ollama. Pastikan Ollama berjalan di port 11434.")?;

        if !response.status().is_success() {
            let error_text = response.text().await.unwrap_or_default();
            error!("Ollama API returned error: {}", error_text);
            return Err(anyhow::anyhow!("Ollama API error: {}", error_text));
        }

        let resp_json: OllamaChatResponsePayload = response
            .json()
            .await
            .context("Gagal membaca respon JSON dari Ollama")?;

        Ok(resp_json.message.content)
    }

    pub fn model_name(&self) -> &str {
        &self.model
    }

    pub fn base_url(&self) -> &str {
        &self.base_url
    }
}
