FROM golang:1.25.3-alpine
WORKDIR /api
COPY . .
RUN go build -o /main .
CMD ["/main"]