export type ValidationError = {
  code: string;
  errors: Record<string, string[]>;
};

export class ValidationErrorException extends Error {
  public constructor(public readonly status: number, public readonly data: ValidationError) {
    super();
  }
}

export class UnknownErrorException extends Error {
  public constructor(public readonly status: number) {
    super();
  }
}
