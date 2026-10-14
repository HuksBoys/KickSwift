use axum::{extract::State, Json};
use std::sync::Arc;

use crate::{models::chat::HealthResponse, services::ollama::OllamaService};

#[derive(Clone)]
pub struct AppState {
    pub ollama: Arc<OllamaService>,
}

pub async fn health_check(State(state): State<AppState>) -> Json<HealthResponse> {
    Json(HealthResponse {
        status: "ok".to_string(),
        service: "KickSwift Rust AI Microservice".to_string(),
        version: "0.1.0".to_string(),
        ollama_url: state.ollama.base_url().to_string(),
    })
}
