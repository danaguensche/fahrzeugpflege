<template>
    <v-sheet class="section-block">
        <DefaultHeader :title="'Kommentare'"></DefaultHeader>
        <v-list dense class="comment-list">
            <v-list-item v-for="comment in comments" :key="comment.id" class="comment-item">
                <v-list-item-content>
                    <div class="d-flex justify-space-between align-center">
                        <v-list-item-title class="comment-author">{{ comment.user.name }} {{ comment.user.email }}</v-list-item-title>
                        <v-btn v-if="userRole === 'admin'" icon @click="deleteComment(comment.id)" variant="text" color="error" size="small">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </div>
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
import { mapState } from 'vuex';

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
    computed: {
        ...mapState('auth', ['userRole']),
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
                const response = await axios.get(`/api/orders/${this.jobId}/comments`);
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
                const response = await axios.post(`/api/orders/${this.jobId}/comments`, {
                    text: this.newCommentText,
                });
                this.comments.unshift(response.data.data); // Add the new comment to the beginning of the array
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
        async deleteComment(commentId) {
            if (!confirm('Sind Sie sicher, dass Sie diesen Kommentar löschen möchten?')) {
                return;
            }

            this.loading = true;
            try {
                await axios.delete(`/api/comments/${commentId}`);
                this.comments = this.comments.filter(comment => comment.id !== commentId);
                this.$emit('show-snackbar', 'Kommentar erfolgreich gelöscht.', 'success');
            } catch (error) {
                console.error('Error deleting comment:', error);
                this.error = 'Fehler beim Löschen des Kommentars.';
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
    font-size: 1.25em;
}

.comment-form {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #eee;
}
</style>
