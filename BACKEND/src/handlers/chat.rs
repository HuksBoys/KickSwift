use axum::{
    extract::State,
    http::StatusCode,
    response::IntoResponse,
    Json,
};
use tracing::error;

use crate::{
    handlers::health::AppState,
    models::chat::{ChatRequest, ChatResponse},
};

pub async fn handle_chat(
    State(state): State<AppState>,
    Json(payload): Json<ChatRequest>,
) -> impl IntoResponse {
    if payload.prompt.trim().is_empty() {
        return (
            StatusCode::BAD_REQUEST,
            Json(ChatResponse {
                status: "error".to_string(),
                response: "Prompt tidak boleh kosong.".to_string(),
                suggested_intent: None,
                model_used: state.ollama.model_name().to_string(),
            }),
        );
    }

    match state
        .ollama
        .generate_chat_response(&payload.prompt, payload.history, payload.field_context)
        .await
    {
        Ok(reply) => {
            // Deteksi intent sederhana untuk membantu front-end UX (opsional)
            let intent = if payload.prompt.to_lowercase().contains("jadwal") || payload.prompt.to_lowercase().contains("jam") {
                Some("VIEW_SCHEDULE".to_string())
            } else if payload.prompt.to_lowercase().contains("booking") || payload.prompt.to_lowercase().contains("pesan") {
                Some("BOOK_FIELD".to_string())
            } else if payload.prompt.to_lowercase().contains("harga") || payload.prompt.to_lowercase().contains("biaya") {
                Some("VIEW_PRICING".to_string())
            } else {
                None
            };

            (
                StatusCode::OK,
                Json(ChatResponse {
                    status: "success".to_string(),
                    response: reply,
                    suggested_intent: intent,
                    model_used: state.ollama.model_name().to_string(),
                }),
            )
        }
        Err(err) => {
            error!("Chat generation failed: {:?}", err);
            (
                StatusCode::INTERNAL_SERVER_ERROR,
                Json(ChatResponse {
                    status: "error".to_string(),
                    response: format!(
                        "Maaf, layanan AI Assistant sedang tidak dapat diakses ({})",
                        err
                    ),
                    suggested_intent: None,
                    model_used: state.ollama.model_name().to_string(),
                }),
            )
        }
    }
}
