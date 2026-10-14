mod config;
mod handlers;
mod models;
mod services;

use std::sync::Arc;
use std::net::SocketAddr;
use axum::{
    routing::{get, post},
    Router,
};
use tower_http::cors::{Any, CorsLayer};
use tower_http::trace::TraceLayer;
use tracing::info;
use tracing_subscriber::{layer::SubscriberExt, util::SubscriberInitExt};

use crate::config::AppConfig;
use crate::handlers::health::AppState;
use crate::services::ollama::OllamaService;

#[tokio::main]
async fn main() -> anyhow::Result<()> {
    // 1. Inisialisasi Logging & Tracing
    tracing_subscriber::registry()
        .with(
            tracing_subscriber::EnvFilter::try_from_default_env()
                .unwrap_or_else(|_| "kickswift_backend=debug,tower_http=debug".into()),
        )
        .with(tracing_subscriber::fmt::layer())
        .init();

    // 2. Load Konfigurasi Environment
    let config = AppConfig::from_env();
    info!("Starting KickSwift Rust AI Microservice with config: {:?}", config);

    // 3. Inisialisasi Layanan Ollama & Shared State
    let ollama_service = Arc::new(OllamaService::new(
        config.ollama_base_url.clone(),
        config.ollama_model.clone(),
    ));

    let app_state = AppState {
        ollama: ollama_service,
    };

    // 4. Konfigurasi CORS (Memungkinkan komunikasi aman dari Laravel & Client Frontend)
    let cors = CorsLayer::new()
        .allow_origin(Any)
        .allow_methods(Any)
        .allow_headers(Any);

    // 5. Inisialisasi Routing Axum
    let app = Router::new()
        .route("/health", get(handlers::health::health_check))
        .route("/api/v1/health", get(handlers::health::health_check))
        .route("/api/v1/chat", post(handlers::chat::handle_chat))
        .layer(cors)
        .layer(TraceLayer::new_for_http())
        .with_state(app_state);

    // 6. Jalankan Server TCP
    let addr: SocketAddr = format!("{}:{}", config.host, config.port).parse()?;
    info!("KickSwift Rust Microservice listening on http://{}", addr);

    let listener = tokio::net::TcpListener::bind(addr).await?;
    axum::serve(listener, app).await?;

    Ok(())
}
