<template>
    <v-sheet class="section-block">
        <DefaultHeader :title="'Kommentare'"></DefaultHeader>
        <v-list dense class="comment-list">
            <v-list-item v-for="comment in comments" :key="comment.id" class="comment-item">
                <v-list-item-content>
                    <v-list-item-title class="comment-author">{{ comment.user.name }}</v-list-item-title>
                    <v-list-item-subtitle class="comment-date">{{ formatDate(comment.created_at) }}</v-list-item-subtitle>
                    <v-list-item-text class="comment-text">{{ comment.comment_text }}</v-list-item-text>
                </v-list-item-content>
            </v-list-item>
            <v-list-item v-if="comments.length === 0">
                <v-list-item-content>
                    <v-list-item-text class="text-grey">Noch keine Kommentare vorhanden.</v-list-item-text>
                </v-list-item-content>
            </v-list-item>
        </v-list>

        <v-form @submit.prevent="addComment" class="comment-form">
            <v-textarea
                v-model="newCommentText"
                label="Neuer Kommentar"
                variant="outlined"
                rows="3"
                clearable
                hide-details="auto"
                class="mb-4"
            ></v-textarea>
            <v-btn type="submit" color="primary" :loading="loading">Kommentar hinzufügen</v-btn>
        </v-form>
    </v-sheet>
</template>

<script>
import axios from 'axios';
import DefaultHeader from '../../Details/DefaultHeader.vue';

export default {
    name: 'CommentsSection',
    components: {
        DefaultHeader,
    },
    props: {
        jobId: {
            type: [String, Number],
            required: true,
        },
    },
    data() {
        return {
            comments: [],
            newCommentText: '',
            loading: false,
            error: null,
        };
    },
    async mounted() {
        await this.fetchComments();
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return '';
            try {
                return new Date(dateString).toLocaleDateString('de-DE', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch {
                return 'Ungültiges Datum';
            }
        },
        async fetchComments() {
            this.loading = true;
            try {
                const response = await axios.get(`/api/jobs/${this.jobId}/comments`);
                this.comments = response.data.data;
            } catch (error) {
                console.error('Error fetching comments:', error);
                this.error = 'Fehler beim Laden der Kommentare.';
                // Optionally, emit an event to show a snackbar in the parent component
                this.$emit('show-snackbar', this.error, 'error');
            } finally {
                this.loading = false;
            }
        },
        async addComment() {
            if (!this.newCommentText.trim()) {
                this.$emit('show-snackbar', 'Kommentar darf nicht leer sein.', 'warning');
                return;
            }

            this.loading = true;
            try {
                const response = await axios.post(`/api/jobs/${this.jobId}/comments`, {
                    text: this.newCommentText,
                });
                this.comments.push(response.data.data); // Assuming the API returns the new comment
                this.newCommentText = '';
                this.$emit('show-snackbar', 'Kommentar erfolgreich hinzugefügt.', 'success');
            } catch (error) {
                console.error('Error adding comment:', error);
                this.error = 'Fehler beim Hinzufügen des Kommentars.';
                this.$emit('show-snackbar', this.error, 'error');
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.section-block {
    margin-top: 24px;
    padding-top: 16px;
    border-top: 1px solid #eee;
}

.comment-list {
    background-color: transparent;
    padding: 0;
}

.comment-item {
    border-bottom: 1px solid #eee;
    padding: 8px 0;
}

.comment-item:last-child {
    border-bottom: none;
}

.comment-author {
    font-weight: bold;
    color: #333;
}

.comment-date {
    font-size: 0.85em;
    color: #777;
    margin-bottom: 4px;
}

.comment-text {
    color: #555;
    white-space: pre-wrap; /* Preserves whitespace and wraps text */
}

.comment-form {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #eee;
}
</style>
