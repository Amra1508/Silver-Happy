FROM golang:1.25.3-alpine AS builder

WORKDIR /api

COPY . .

RUN go build -o /main .

FROM alpine:latest AS production

RUN apk add --no-cache tzdata

COPY --from=builder /main /main

EXPOSE 8082

CMD ["/main"]