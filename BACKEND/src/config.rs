use std::env;

#[derive(Clone, Debug)]
pub struct AppConfig {
    pub host: String,
    pub port: u16,
    pub ollama_base_url: String,
    pub ollama_model: String,
}

impl AppConfig {
    pub fn from_env() -> Self {
        dotenvy::dotenv().ok();

        let host = env::var("HOST").unwrap_or_else(|_| "127.0.0.1".to_string());
        let port = env::var("PORT")
            .unwrap_or_else(|_| "5000".to_string())
            .parse::<u16>()
            .expect("PORT must be a valid number");
        let ollama_base_url = env::var("OLLAMA_BASE_URL")
            .unwrap_or_else(|_| "http://127.0.0.1:11434".to_string());
        let ollama_model = env::var("OLLAMA_MODEL")
            .unwrap_or_else(|_| "llama3:latest".to_string());

        Self {
            host,
            port,
            ollama_base_url,
            ollama_model,
        }
    }
}
