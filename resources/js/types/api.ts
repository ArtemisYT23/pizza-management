export type IngredientDto = {
    id: string;
    name: string;
    created_at: string;
    updated_at: string;
};

export type PizzaDto = {
    id: string;
    name: string;
    description: string | null;
    price: string;
    image_url: string | null;
    ingredients: IngredientDto[];
    created_at: string;
    updated_at: string;
};

export type OrderUserDto = {
    id: string;
    name: string;
    email: string;
};

export type OrderDto = {
    id: string;
    ordered_at: string;
    user: OrderUserDto;
    pizza: PizzaDto;
    created_at: string;
};

export type ApiCollection<T> = { data: T[] };
export type ApiResource<T> = { data: T };

export type ValidationErrors = Record<string, string[]>;

export class ApiError extends Error {
    constructor(
        message: string,
        public readonly status: number,
        public readonly errors?: ValidationErrors,
    ) {
        super(message);
        this.name = 'ApiError';
    }
}
