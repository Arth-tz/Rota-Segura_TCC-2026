export interface Pessoa {
    id_pessoa?: number;
    nome: string;
    cpf: string;
    data_nascimento: string;
    telefone: string;
    foto_url?: string;
    ativo?: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface Usuario {
    id_usuario?: number;
    id_pessoa?: number;
    email: string;
    role: 'admin' | 'motorista' | 'responsavel' | 'passageiro';
    ativo?: boolean;
    ultimo_login?: string;
    pessoa?: Pessoa;
}

export interface Responsavel {
    id_responsavel?: number;
    id_usuario?: number;
    tipo_responsavel: string;
    telefone_emergencia?: string;
    data_responsavel_ate?: string;
    usuario?: Usuario;
}

export interface Passageiro {
    id_passageiro?: number;
    id_pessoa?: number;
    id_usuario?: number;
    observacoes_medicas?: string;
    ativo?: boolean;
    data_inscricao?: string;
    pessoa?: Pessoa;
}

export interface Endereco {
    id_endereco?: number;
    logradouro: string;
    numero: string;
    complemento?: string;
    bairro: string;
    cidade: string;
    estado: string;
    cep: string;
    latitude?: number;
    longitude?: number;
}
